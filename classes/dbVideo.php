<?php
class dbVideo extends DB {
    
    public function getVideoList(){
        return $this->pdo->query("SELECT * FROM videotest WHERE del_flg=0 ORDER BY created DESC, id DESC")->fetchAll();
    }

    public function getVideo($id){
        $stmt = $this->pdo->prepare("SELECT * FROM videotest WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function addVideo($upfile){
        $stmt = $this->pdo->prepare("INSERT INTO videotest(videoname,path) VALUES(?,?)");
        $stmt->execute([
            $upfile['name'],
            $_SERVER['REQUEST_TIME'].'.'.pathinfo($upfile['name'], PATHINFO_EXTENSION)
        ]);
    }

    public function editVideo($id,$videoname,$ext){
        $stmt = $this->pdo->prepare("UPDATE videotest SET videoname=? WHERE id=?");
        $stmt->execute([$videoname.'.'.$ext,$id]);
    }

    public function delVideo($id){
        $stmt = $this->pdo->prepare("UPDATE videotest SET del_flg=1 WHERE id=?");
        $stmt->execute([$id]);
    }

}
