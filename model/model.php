<?php
include_once "db/db.php";
class model extends koneksi{
	public function selectprepare($table, $field = null, $params=null, $where=null, $other=null){
		$sql = "SELECT";
		$kolom = null;
		if($field != null){
			foreach($field as $key){
				$kolom .= ", ".$key."";
			}
			$sql .= substr($kolom, 1)."";
		}else{
			$sql .= " * ";
		}
		$sql .= " FROM $table";
		
		if($params != null){
			$sql .= " WHERE $where";
			$sql .= " $other";
			$query = $this->db->prepare($sql);
			foreach ($params as $kolom => &$isian){
				$query->bindParam($kolom, $isian);  //echo "<p>$kolom, $isian</p>";
			}
			$query->execute($params);
		}else{
			$sql .= " $other";
			$query = $this->db->prepare($sql);
			$query->execute();
		}
		//echo "<br/><br/>".$sql;
		return $query;
	}

	public function insertprepare($table, $field = null, $params=null){
		$sql = "INSERT INTO $table ";
		$kolom = null;
		$value = null;
		foreach($field as $key => $nilai){
			$kolom .= ", ".$key."";
			$value .= ", '".$nilai."'";
		}
		$sql .= "(".substr($kolom, 1).")";
		$sql .= " VALUES (".substr($value, 1).")";

		$query = $this->db->prepare($sql);
		foreach ($params as $kolom => &$isian){
			$query->bindParam($kolom, $isian);  //echo "<p>$kolom, $isian</p>";
		}
		$query->execute($params);
		//echo "<br/><br/>".$sql;
		return $query;
	}
	public function hapusprepare($table, $params=null, $where=null){
		$sql = "DELETE FROM $table";
		if($params != null){
			$sql .= " WHERE $where";
			$query = $this->db->prepare($sql);
			foreach ($params as $kolom => &$isian){
				$query->bindParam($kolom, $isian);  //echo "<p>$params, $isian</p>";
			}
			$query->execute($params);
		}else{
			$query = $this->db->prepare($sql);
			$query->execute();
		}
		//echo "<br/><br/>".$sql;
		return $query;
	}
	public function updateprepare($table, $field = null, $params=null, $where=null){
		$sql = "UPDATE $table set ";
			$dump = null;
			foreach($field as $key => $nilai){
				$dump .= ", ".$key." = '".$nilai."'";
			}
			$sql .= substr($dump, 1);
			
		if($params != null){
			$sql .= " WHERE $where";
			$query = $this->db->prepare($sql);
			foreach ($params as $kolom => &$isian){
				$query->bindParam($kolom, $isian);  //echo "<p>$kolom, $isian</p>";
			}
			$query->execute($params);
		}else{
			$query = $this->db->prepare($sql);
			$query->execute();
		}
		//echo "<br/><br/>".$sql;
		return $query;
	}
	public function selectcount($table, $params=null, $where=null, $other=null){
		$sql = "select count(*) as jlh from $table";
		if($params != null){
			$sql .= " WHERE $where";
			$sql .= " $other";
			$query = $this->db->prepare($sql);
			foreach ($params as $kolom => &$isian){
				$query->bindParam($kolom, $isian);  //echo "<p>$params, $isian</p>";
			}
			$query->execute($params);
		}else{
			$sql .= " $other";
			$query = $this->db->prepare($sql);
			$query->execute();
		}
		//echo "<br/><br/>".$sql;
		return $query;
	}
	public function truncate($table){
		$sql = "TRUNCATE TABLE $table";
		$query = $this->db->query($sql);
		$query->execute();
		return $query;
	}
}?>