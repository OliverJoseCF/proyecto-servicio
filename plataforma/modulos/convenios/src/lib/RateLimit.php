<?php
/**
 * Rate limiter de archivo para proteger endpoints sensibles (login, sugerir).
 * No requiere base de datos — usa archivos en el directorio temporal del sistema.
 */
class RateLimit {
    private string $dir;
    private int    $maxAttempts;
    private int    $windowSeconds;

    public function __construct(int $maxAttempts = 5, int $windowSeconds = 900) {
        $this->maxAttempts   = $maxAttempts;
        $this->windowSeconds = $windowSeconds;
        $this->dir           = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'tecsj_rl' . DIRECTORY_SEPARATOR;

        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0700, true);
        }
    }

    private function key(string $identifier): string {
        return $this->dir . hash('sha256', $identifier) . '.json';
    }

    /** Registra un intento fallido. */
    public function record(string $identifier): void {
        $file     = $this->key($identifier);
        $attempts = $this->load($file);
        $now      = time();

        $attempts = array_values(
            array_filter($attempts, fn(int $t): bool => ($now - $t) < $this->windowSeconds)
        );
        $attempts[] = $now;

        file_put_contents($file, json_encode($attempts), LOCK_EX);
    }

    /** Devuelve true si el identificador supera el límite de intentos. */
    public function isBlocked(string $identifier): bool {
        $file    = $this->key($identifier);
        $recent  = array_filter(
            $this->load($file),
            fn(int $t): bool => (time() - $t) < $this->windowSeconds
        );
        return count($recent) >= $this->maxAttempts;
    }

    /** Limpia el historial tras una acción exitosa. */
    public function reset(string $identifier): void {
        $file = $this->key($identifier);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /** Cuántos intentos quedan disponibles. */
    public function remaining(string $identifier): int {
        $file   = $this->key($identifier);
        $recent = array_filter(
            $this->load($file),
            fn(int $t): bool => (time() - $t) < $this->windowSeconds
        );
        return max(0, $this->maxAttempts - count($recent));
    }

    private function load(string $file): array {
        if (!file_exists($file)) return [];
        $data = json_decode((string) file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }
}
