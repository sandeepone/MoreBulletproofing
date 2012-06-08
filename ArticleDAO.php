<?php
class ArticleDAO implements IArticleDAO
{
    public function getArticles($sectionId) {
        $db = new PDO(
            "mysql:host=localhost;dbname=bulletproof", 
            "root", "");

        $result = $db->query("SELECT a.id, a.title FROM features f LEFT JOIN articles a ON f.article_id = a.id AND f.section_id = 1");
        $articles = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $articles[] = $row;
        }
        $result->closeCursor();

        return $articles;
    }

    public function save($article) {
        $db = new PDO("mysql:host=localhost;dbname=bulletproof",
            "root", "");

        $stmt = $db->prepare("INSERT INTO articles (title, description, content, preview_image, section_id) value (:title, :description, :content, :preview_image, :section_id)");
        $stmt->execute($article);
        return true;
    }
}

