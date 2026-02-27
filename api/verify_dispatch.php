<?php
require_once 'config/db.php';
require_once 'controllers/CompanyController.php';

// Mock authentication for admin
class MockRequest {
    public function authenticate() {
        return ['id' => 'admin-id', 'role' => 'admin'];
    }
}

$database = new Database();
$db = $database->getConnection();

// We need to inject the mock or hit the endpoint
// For simplicity, let's just instantiate and call the method, mocking the $this->authenticate()
// Since CompanyController extends Controller and we don't want to mess with that, 
// let's just hit the actual endpoint via a simplified internal call or just Hit it via CLI if possible.
// Better: hit the actual endpoint with a token!

$token = 'RAPPEL_PHP_TOKEN_PLACEHOLDER'; // This should have been replaced if I were in the browser
// But since I'm in CLI, I'll just call the method directly by creating a child class that bypasses auth for this test.

class TestCompanyController extends CompanyController {
    public function authenticate() {
        return ['id' => 'admin-id', 'role' => 'admin'];
    }
}

$controller = new TestCompanyController();
echo "--- TRIGGERING AUTO-DISPATCH ---\n";
$controller->autoDispatchLeads();
echo "\n--- DISPATCH TRIGGERED ---\n";
?>
