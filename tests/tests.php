<?php

require_once __DIR__ . '/testframework.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modules/database.php';
require_once __DIR__ . '/../modules/page.php';

$testFramework = new TestFramework();

// Test 1: Verifică conexiunea la baza de date
function testDbConnection() {
    global $config;
    try {
        $db = new Database($config["dbSavvy($config["db"]["path"]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Test 2: Verifică metoda Count
function testDbCount() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    // Creează un tabel temporar pentru test
    $db->Execute("CREATE TABLE test_table (id INTEGER PRIMARY KEY, name TEXT)");
    
    // Testează tabela goală
    $count1 = $db->Count("test_table");
    
    // Adaugă o înregistrare
    $db->Create("test_table", ["name" => "Test"]);
    
    // Verifică dacă numărul a crescut
    $count2 = $db->Count("test_table");
    
    // Curăță
    $db->Execute("DROP TABLE test_table");
    
    return $count1 === 0 && $count2 === 1;
}

// Test 3: Verifică metoda Create
function testDbCreate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    $db->Execute("CREATE TABLE test_table (id INTEGER PRIMARY KEY, name TEXT, value INTEGER)");
    
    $data = [
        "name" => "Test Item",
        "value" => 42
    ];
    
    $id = $db->Create("test_table", $data);
    
    // Verifică dacă înregistrarea există
    $result = $db->Read("test_table", $id);
    
    $db->Execute("DROP TABLE test_table");
    
    return $id > 0 && 
           $result["name"] === "Test Item" && 
           $result["value"] === 42;
}

// Test 4: Verifică metoda Read
function testDbRead() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    $db->Execute("CREATE TABLE test_table (id INTEGER PRIMARY KEY, name TEXT)");
    
    $data = ["name" => "Read Test"];
    $id = $db->Create("test_table", $data);
    
    $result = $db->Read("test_table", $id);
    
    $db->Execute("DROP TABLE test_table");
    
    return $result["name"] === "Read Test";
}

// Test 5: Verifică metoda Update
function testDbUpdate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    $db->Execute("CREATE TABLE test_table (id INTEGER PRIMARY KEY, name TEXT, value INTEGER)");
    
    $data = [
        "name" => "Initial",
        "value" => 1
    ];
    $id = $db->Create("test_table", $data);
    
    $updateData = [
        "name" => "Updated",
        "value" => 2
    ];
    
    $db->Update("test_table", $id, $updateData);
    $result = $db->Read("test_table", $id);
    
    $db->Execute("DROP TABLE test_table");
    
    return $result["name"] === "Updated" && $result["value"] === 2;
}

// Test 6: Verifică metoda Delete
function testDbDelete() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    $db->Execute("CREATE TABLE test_table (id INTEGER PRIMARY KEY, name TEXT)");
    
    $data = ["name" => "To Delete"];
    $id = $db->Create("test_table", $data);
    
    $db->Delete("test_table", $id);
    $result = $db->Read("test_table", $id);
    
    $db->Execute("DROP TABLE test_table");
    
    return $result === false;
}

// Test 7: Verifică metoda Execute
function testDbExecute() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    try {
        $db->Execute("CREATE TABLE test_table (id INTEGER PRIMARY KEY)");
        $db->Execute("INSERT INTO test_table (id) VALUES (1)");
        $db->Execute("DROP TABLE test_table");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Test 8: Verifică metoda Fetch
function testDbFetch() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    $db->Execute("CREATE TABLE test_table (id INTEGER PRIMARY KEY, name TEXT)");
    
    $data = [
        ["name" => "Item 1"],
        ["name" => "Item 2"]
    ];
    
    foreach ($data as $item) {
        $db->Create("test_table", $item);
    }
    
    $results = $db->Fetch("SELECT * FROM test_table");
    
    $db->Execute("DROP TABLE test_table");
    
    return count($results) === 2 && 
           $results[0]["name"] === "Item 1" && 
           $results[1]["name"] === "Item 2";
}

// Test 9: Verifică constructorul Page
function testPageConstruct() {
    try {
        $page = new Page(__DIR__ . '/../templates/index.tpl');
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Test 10: Verifică metoda Render
function testPageRender() {
    $page = new Page(__DIR__ . '/../templates/index.tpl');
    
    $data = [
        "title" => "Test Title",
        "content" => "Test Content",
        "footer" => "Test Footer"
    ];
    
    $output = $page->Render($data);
    
    return strpos($output, "Test Title") !== false &&
           strpos($output, "Test Content") !== false &&
           strpos($output, "Test Footer") !== false;
}

// Adaugă testele
$testFramework->add('Database connection', 'testDbConnection');
$testFramework->add('Table count', 'testDbCount');
$testFramework->add('Data create', 'testDbCreate');
$testFramework->add('Data read', 'testDbRead');
$testFramework->add('Data update', 'testDbUpdate');
$testFramework->add('Data delete', 'testDbDelete');
$testFramework->add('Query execute', 'testDbExecute');
$testFramework->add('Query fetch', 'testDbFetch');
$testFramework->add('Page construction', 'testPageConstruct');
$testFramework->add('Page rendering', 'testPageRender');

// Rulează testele
$testFramework->run();

echo $testFramework->getResult();