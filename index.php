<?php

const ROOT = __DIR__;
require_once(ROOT . '/utils/NewsManager.php');
require_once(ROOT . '/utils/CommentManager.php');

foreach (NewsManager::getInstance()->listNews() as $news) {
    echo "=========================================\n";
    echo "############ NEWS ############\n";
    echo "Title: " . $news->getTitle() . "\n";
    echo "-----------------------------------------\n";
    echo $news->getBody() . "\n";
    echo "-----------------------------------------\n";
    echo "Comments:\n";

    if (count($news->getComments()) > 0) {
        foreach ($news->getComments() as $comment) {
            echo "  - Comment " . $comment->getId() . ": " . $comment->getBody() . "\n";
        }
    } else {
        echo "  No comments yet.\n";
    }

    echo "=========================================\n\n";
}
