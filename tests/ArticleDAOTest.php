<?php
require_once "../vendor/autoload.php";
require_once "../include/IArticleDAO.php";
require_once "../include/ArticleDAO.php";


class PHPUnit_Extensions_Database_Operation_MySQL55Truncate extends PHPUnit_Extensions_Database_Operation_Truncate
{
    public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet) {
        $connection->getConnection()->query("SET @PHAKE_PREV_foreign_key_checks = @@foreign_key_checks");
        $connection->getConnection()->query("SET foreign_key_checks = 0");
        parent::execute($connection, $dataSet);
        $connection->getConnection()->query("SET foreign_key_checks = @PHAKE_PREV_foreign_key_checks");
    }
}

class ArticleDAOTest extends PHPUnit_Extensions_Database_TestCase
{
    public function getConnection() {
        $db = new PDO(
            "mysql:host=localhost;dbname=bulletproof", 
            "dbuser", "dbpass");
        return $this->createDefaultDBConnection($db, "bulletproof");
    }

    public function getSetUpOperation() {
        // whether you want cascading truncates
        // set false if unsure
        $cascadeTruncates = false;

        return new PHPUnit_Extensions_Database_Operation_Composite(array(
            new PHPUnit_Extensions_Database_Operation_MySQL55Truncate($cascadeTruncates),
            PHPUnit_Extensions_Database_Operation_Factory::INSERT()
        ));
    }

    public function getDataSet() {
        return $this->createXMLDataSet("seed.xml");
    }

    public function testSaveArticle() {
        $article = new ArticleDAO();
        $article->save(array(
            "title" => "PHP is Great!",
            "description" => "Lorem ipsum dolor sit amet",
            "content" => "Aliquam scelerisque rhoncus porttitor. Nullam eget pulvinar magna. In vel lectus ut diam adipiscing porta vitae id nisi.",
            "preview_image" => "php.jpg",
            "section_id" => 1));

        $resultingTable = $this->getConnection()
            ->createQueryTable("articles",
            "SELECT * FROM articles");
        
        $expectedTable = $this->createXmlDataSet("expectedArticles.xml")
            ->getTable("articles");
        $this->assertTablesEqual($expectedTable, $resultingTable);   
    }
}

