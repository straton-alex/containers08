<?php

require_once __DIR__ . '/modules/database.php';
require_once __DIR__ . '/modules/page.php';
require_once __DIR__ . '/config.php';

try {
    $db = new Database($config["db"]["path"]);
    $page = new Page(__DIR__ . '/templates/index.tpl');

    // Securizare parametru GET
    $pageId = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if ($pageId === false || $pageId === null) {
        throw new Exception("Invalid page ID");
    }

    $data = $db->Read("page", $pageId);
    if (!$data) {
        throw new Exception("Page not found");
    }

    echo $page->Render($data);
} catch (Exception $e) {
    header("HTTP/1.1 404 Not Found");
    echo "Error: " . htmlspecialchars($e->getMessage());
}