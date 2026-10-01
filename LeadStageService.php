<?php
declare(strict_types=1);

final class LeadStageService
{
    public const STAGES = [
        'New Lead',
        'Contacted',
        'Qualified',
        'Proposal Sent',
        'Won',
        'Lost',
    ];

    /**
     * Legal stage graph.
     * Won/Lost are terminal stages.
     */
    private const ALLOWED_TRANSITIONS = [
        'New Lead'     => ['Contacted'],
        'Contacted'    => ['Qualified'],
        'Qualified'    => ['Proposal Sent'],
        'Proposal Sent'=> ['Won', 'Lost'],
        'Won'          => [],
        'Lost'         => [],
    ];

    public function __construct(private PDO $pdo)
    {
    }

    public function transition(
        int $leadId,
        int $userId,
        string $newStage,
        array $metadata = []
    ): array {
        if ($leadId <= 0) {
            throw new InvalidArgumentException('Invalid lead_id.');
        }

        if (!in_array($newStage, self::STAGES, true)) {
            throw new InvalidArgumentException('Invalid new_stage.');
        }

        $this->pdo->beginTransaction();

        try {
            $lead = $this->getLeadForUpdate($leadId);

            if (!$lead) {
                throw new RuntimeException('Lead not found.');
            }

            $oldStage = $lead['stage'];

            if ($oldStage === $newStage) {
                throw new DomainException('Lead is already in the requested stage.');
            }

            if (!$this->isAllowedTransition($oldStage, $newStage)) {
                throw new DomainException(
                    "Illegal stage transition: {$oldStage} -> {$newStage}."
                );
            }

            $transitionMetadata = [
                'requested_at' => gmdate('c'),
                'from_stage'   => $oldStage,
                'to_stage'     => $newStage,
                'details'      => $metadata,
            ];

            $update = $this->pdo->prepare(
                'UPDATE leads
                 SET stage = :stage, updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id'
            );
            $update->execute([
                ':stage' => $newStage,
                ':id'    => $leadId,
            ]);

            $log = $this->pdo->prepare(
                'INSERT INTO lead_activity_log
                    (lead_id, user_id, old_stage, new_stage, transition_metadata, created_at)
                 VALUES
                    (:lead_id, :user_id, :old_stage, :new_stage, :metadata, CURRENT_TIMESTAMP)'
            );

            $log->execute([
                ':lead_id'  => $leadId,
                ':user_id'  => $userId,
                ':old_stage'=> $oldStage,
                ':new_stage'=> $newStage,
                ':metadata' => json_encode(
                    $transitionMetadata,
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                ),
            ]);

            $activityId = (int)$this->pdo->lastInsertId();

            $this->pdo->commit();

            return [
                'lead_id'     => $leadId,
                'old_stage'   => $oldStage,
                'new_stage'   => $newStage,
                'activity_id' => $activityId,
                'message'     => 'Lead stage transitioned successfully.',
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function isAllowedTransition(string $oldStage, string $newStage): bool
    {
        return in_array(
            $newStage,
            self::ALLOWED_TRANSITIONS[$oldStage] ?? [],
            true
        );
    }

    public function getAllowedNextStages(string $stage): array
    {
        return self::ALLOWED_TRANSITIONS[$stage] ?? [];
    }

    private function getLeadForUpdate(int $leadId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, stage, created_at, updated_at
             FROM leads
             WHERE id = :id
             FOR UPDATE'
        );
        $stmt->execute([':id' => $leadId]);

        $lead = $stmt->fetch();

        return $lead ?: null;
    }
}
