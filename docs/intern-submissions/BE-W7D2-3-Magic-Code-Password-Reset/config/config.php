<?php
declare(strict_types=1);

/*
 * Copy this file's values to match your XAMPP/MySQL setup.
 * For production, move secrets to environment variables.
 */

const DB_HOST = '127.0.0.1';
const DB_NAME = 'magic_auth_demo';
const DB_USER = 'root';
const DB_PASS = '';

const MAGIC_CODE_EXPIRY_MINUTES = 10;
const PASSWORD_RESET_EXPIRY_MINUTES = 30;
const MAX_MAGIC_ATTEMPTS = 5;
const MAX_RESET_ATTEMPTS = 5;
