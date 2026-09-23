# BE-W7D5-1: Lead Search (Core PHP)

## Description
Core PHP implementation for Lead Search.

## Logic
- An empty search returns all leads.
- Submitting a partial string (e.g., "Ah") searches name, email, or phone case-insensitively using `strpos` & `array_filter`.
