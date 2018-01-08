<?php
class DB {

    protected $pdo;

    function __construct(){
        if(!is_dir(__DIR__.'/../db')) mkdir(__DIR__.'/../db');
        $pdo = new PDO('sqlite:'.__DIR__.'/../db/db.sqlite');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo = $pdo;
    }

    public function createVideoList(){
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS videotest (
                id        INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                videoname VARCHAR NOT NULL,
                path      VARCHAR NOT NULL,
                created   DATE NOT NULL DEFAULT (DATETIME('now','+9 hours')),
                modified  DATE NOT NULL DEFAULT (DATETIME('now','+9 hours')),
                del_flg   INTEGER DEFAULT 0
             );"
        );
        $this->pdo->exec(
            "CREATE TRIGGER IF NOT EXISTS videotest_modified AFTER UPDATE on videotest  
                BEGIN  
                    UPDATE videotest SET modified = DATETIME('now','+9 hours') WHERE id = old.id;  
                END;"
        );
        $this->pdo->exec(
            "UPDATE videotest SET del_flg=0
             WHERE id in(1,2,3)
             AND (SELECT COUNT(*) FROM videotest WHERE del_flg=0) = 0"
        );
    }
    
}
