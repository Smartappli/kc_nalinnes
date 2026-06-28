<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/database.php';

final class DatabaseConfigTest extends TestCase {
    /** @var array<string, array{defined: bool, value: string|null}> */
    private array $originalEnv = [];

    protected function tearDown(): void {
        foreach ($this->originalEnv as $name => $state) {
            if ($state['defined']) {
                $this->setEnvValue($name, $state['value'] ?? '');
            }
            else {
                $this->unsetEnvValue($name);
            }
        }

        $this->originalEnv = [];
    }

    public function testEnvFileLoadsQuotedAndUnquotedValues(): void {
        $path = tempnam(sys_get_temp_dir(), 'kc-env-test-');
        $this->assertIsString($path);

        file_put_contents($path, "DB_HOST=localhost\nDB_NAME=\"kc test\"\nexport DB_USER='member_user'\nDB_PASS=secret\n");

        foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $name) {
            $this->setEnv($name, null);
        }

        load_env_file($path);
        @unlink($path);

        $this->assertSame('localhost', env_value('DB_HOST'));
        $this->assertSame('kc test', env_value('DB_NAME'));
        $this->assertSame('member_user', env_value('DB_USER'));
        $this->assertSame('secret', env_value('DB_PASS'));
    }

    public function testExplicitEnvironmentValueWinsOverEnvFile(): void {
        $path = tempnam(sys_get_temp_dir(), 'kc-env-test-');
        $this->assertIsString($path);

        file_put_contents($path, "DB_NAME=file_value\n");
        $this->setEnv('DB_NAME', 'server_value');

        load_env_file($path);
        @unlink($path);

        $this->assertSame('server_value', env_value('DB_NAME'));
    }

    public function testDatabaseConfigRequiresExplicitConnectionSettings(): void {
        foreach (['DB_DSN', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $name) {
            $this->setEnv($name, null);
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DB_HOST est manquant');

        database_connection_config();
    }

    public function testDatabaseConfigBuildsMysqlDsn(): void {
        $this->setEnv('DB_DSN', null);
        $this->setEnv('DB_HOST', 'db.example.test');
        $this->setEnv('DB_NAME', 'kc_nalinnes');
        $this->setEnv('DB_CHARSET', 'utf8mb4');
        $this->setEnv('DB_USER', 'kc_user');
        $this->setEnv('DB_PASS', 'kc_pass');

        $this->assertSame(
            ['mysql:dbname=kc_nalinnes;host=db.example.test;charset=utf8mb4', 'kc_user', 'kc_pass'],
            database_connection_config()
        );
    }

    public function testDatabaseConfigUsesDsnWhenProvided(): void {
        $this->setEnv('DB_DSN', 'mysql:dbname=kc;host=db;charset=utf8mb4');
        $this->setEnv('DB_HOST', null);
        $this->setEnv('DB_NAME', null);
        $this->setEnv('DB_USER', 'kc_user');
        $this->setEnv('DB_PASS', 'kc_pass');

        $this->assertSame(
            ['mysql:dbname=kc;host=db;charset=utf8mb4', 'kc_user', 'kc_pass'],
            database_connection_config()
        );
    }

    private function setEnv(string $name, ?string $value): void {
        if (!array_key_exists($name, $this->originalEnv)) {
            $current = getenv($name);
            $this->originalEnv[$name] = [
                'defined' => $current !== false || isset($_ENV[$name]) || isset($_SERVER[$name]),
                'value' => $current !== false ? (string)$current : ($_ENV[$name] ?? $_SERVER[$name] ?? null),
            ];
        }

        if ($value === null) {
            $this->unsetEnvValue($name);
            return;
        }

        $this->setEnvValue($name, $value);
    }

    private function setEnvValue(string $name, string $value): void {
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        @putenv($name . '=' . $value);
    }

    private function unsetEnvValue(string $name): void {
        unset($_ENV[$name], $_SERVER[$name]);
        @putenv($name);
    }
}
