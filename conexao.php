<?php
class conexao{

	public $ip='localhost';
	public $banco='servidor';
	public $usuario='root';
	public $senha='';

	public $con;
	public $tabelas;

	public $erro=false;
	//--------------------------------------------------
	public function __construct($diretorio=false,$val=false){
		if($val==true){
			$this->ip=$val['ip'];
			$this->banco=$val['banco'];
			$this->usuario=$val['usuario'];
			$this->senha=$val['senha'];
			$this->set_conexao($diretorio,$val);
		}else if($diretorio==true){
			//puxa do include
			require $diretorio;
			//$val=$base_tecxp;
			$this->ip=$val['ip'];
			$this->banco=$val['banco'];
			$this->usuario=$val['usuario'];
			$this->senha=$val['senha'];
		}

	}//metudo
	//--------------------------------------------------
	public function set_conexao($dir,$val){
		if($dir==true){
			$arq='<?php '.
			'$val=['.
			'"ip"=>"'.$val['ip'].'",'.
			'"banco"=>"'.$val['banco'].'",'.
			'"usuario"=>"'.$val['usuario'].'",'.
			'"senha"=>"'.$val['senha'].'"'.
			']; ?>';
			$this->set_arquivo($dir,$arq);
		}
	}
	//--------------------------------------------------
	public function verifica_servidor($verifica_bd=false){
		$dao=mysqli_connect($this->ip,$this->usuario,$this->senha);//or die($this->teste());
		//print_r($dao);
    if( !is_object($dao)){
      $this->erro="Falha na conexão com servidor de dados!";
			$this->con=false;
      return false;
    }else{
			$this->erro="Servidor conectado com sucesso!";
			$this->con=$dao;
			$re=true;
			if($verifica_bd==true){
				if(mysqli_select_db($this->con,$this->banco)){
					mysqli_set_charset($this->con,"utf8");
					$this->erro= "Banco de dados conectado";
					$re= true;
				}else{
					$this->erro= "Banco não conectado";
					$re= false;
				}
			}
			return $re;
    }

	}//metodo
	//--------------------------------------------------
	public function conecta(){
		if(!is_object($this->con)){
			$dao=mysqli_connect($this->ip,$this->usuario,$this->senha);
			mysqli_select_db($dao,$this->banco);
			mysqli_set_charset($dao,"utf8");
			$this->con=$dao;
		}
		return $this->con;
		//mysql_close($con);
	}//metodo
	//--------------------------------------------------
	public function fecha_conexao(){
		if(is_object($this->con)){
			mysqli_close($this->con);
			$this->con=null;
		}
	}//metodo
	//--------------------------------------------------
	public function query_id($sql){
		$con=$this->conecta();
		$mysqli=mysqli_query($con,$sql);

		if($erro_saida=mysqli_error($con)){
			$this->erro=$erro_saida;
			return false;
		}else{
			$this->erro="Sql enviado com sucesso!";
			return mysqli_insert_id($con);
		}
	}//metodo
	//--------------------------------------------------
	public function query($sql,$dao=false){
		$con=$this->conecta();
		mysqli_multi_query($con,$sql);

		if($erro_saida=mysqli_error($con)){
			$this->erro=$erro_saida;
			return false;
		}else{
			$this->erro="Sql enviado com sucesso!";
			if($dao==true){
				return mysqli_store_result($con);
			}else{
				return true;
			}
		}
	}//metodo
	//---------------------------------------------------------
	public function query_array($query){
		$query=$this->query($query,true);
		$val=[];
		while($dao = mysqli_fetch_array($query, MYSQLI_ASSOC)){
			$val[]= $dao;
		}//while
		return $val;
	}//metodo
	//------------------------------------------------------------
	public function query_arquivo($diretorio){
		$this->erro='';
		$arquivo = fopen ($diretorio, 'r');
		$sql=feof($arquivo);
		while(!feof($arquivo)){
			$linha = fgets($arquivo, 1024);
			@$sql=$sql.$linha.' ';
		}//while
		fclose($arquivo);
		//cria usuarios
		$re=$this->query($sql);
			//$re=$this->add_adm();
		//}//if
		return $re;
	}//metodo
	//------------------------------------------------------------






	public function exe_global($tipo){
		//$this->erro='';
		//$tabelas=$this->query_array("show tables");
		if(!is_array($this->tabelas)){
			$this->tabelas=$this->query_array("show tables");
		}
		$retorno_erro='';
		for($i=0;$i<count($this->tabelas);$i++){
			echo $tabela= $this->tabelas[$i]['Tables_in_'.$this->banco];
			echo "\n".$sql=$this->tipo_global($tipo,$tabela);
			if( $this->query($sql) ){
				$retorno_erro=$retorno_erro." Comando (".$sql.") executado com sucesso!\n";
			}else{
				$retorno_erro=$retorno_erro." Comando (".$sql.") Erro: ".$this->erro."\n";
			}
			//$retorno_erro=$retorno_erro.' '.$this->erro.';';
		}//for
		return $retorno_erro;
	}//metodo
	//----------------------------------------------------------
	public function tipo_global($tipo,$tabela,$coluna=false,$tabela_ref=false,$id_fk=false,$coluna2=false,$tabela2=false){
		$array=[
			'remove_fk'=>"alter table ".$tabela." drop foreign key ".$coluna,
			'remove_tab'=>"drop table ".$tabela,
			'backup_fk'=>
			"ALTER TABLE ".$tabela2." ADD FOREIGN KEY (".$coluna2.") REFERENCES ".$tabela_ref."(".$id_fk.");"
		];
		return $array[$tipo];
	}
	//-----------------------------------------------------------
	public function global_fk($tipo,$exe=false){
		if(!is_array($this->tabelas)){
			$this->tabelas=$this->query_array("show tables");
		}
		$retorno_erro='';
		$sql=[];
		foreach ($this->tabelas as $tb) {
			$sql1['remove_fk']="
			SELECT *,
			('false') as REFERENCED_TABLE_NAME,
			('false') as REFERENCED_COLUMN_NAME,
			('false') as COLUMN_NAME,
			('false') as TABLE_NAME
			FROM information_schema.REFERENTIAL_CONSTRAINTS a
			WHERE a.CONSTRAINT_SCHEMA = '".$this->banco."' AND a.TABLE_NAME = '".$tb['Tables_in_'.$this->banco]."'";
			$sql1['backup_fk']="
			SELECT
			  TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
			FROM
			  INFORMATION_SCHEMA.KEY_COLUMN_USAGE
			WHERE
			  REFERENCED_TABLE_SCHEMA = '".$this->banco."' AND
			  REFERENCED_TABLE_NAME = '".$tb['Tables_in_'.$this->banco]."'
			";
			/*[TABLE_NAME] => tbcadastro
            [COLUMN_NAME] => id_login
            [CONSTRAINT_NAME] => tbcadastro_ibfk_1
            [REFERENCED_TABLE_NAME] => tblogin
            [REFERENCED_COLUMN_NAME] => id_login*/
			$fks=$this->query_array($sql1[$tipo]);
			print_r($fks);
			foreach ($fks as $fk) {
				echo "\n".$sql=$this->tipo_global(
					$tipo,
					$tb['Tables_in_'.$this->banco],
					$fk['CONSTRAINT_NAME'],
					$fk['REFERENCED_TABLE_NAME'],
					$fk['REFERENCED_COLUMN_NAME'],
					$fk['COLUMN_NAME'],
					$fk['TABLE_NAME']
				);
				if($exe==true){
					if($this->query($sql)){
						$retorno_erro=$retorno_erro." Comando (".$sql.") executado com sucesso!\n";
					}else{
						$retorno_erro=$retorno_erro." Comando (".$sql.") Erro: ".$this->erro."\n";
					}
				}else{
					$retorno_erro=$retorno_erro.$sql."\n";
				}
			}
		}
		return $retorno_erro;
	}
	//------------------------------------------------------------
	public function remove_fk(){
		return $this->global_fk('remove_fk',true);
	}
	//------------------------------------------------------------
	public function backup_fk(){
		return $this->global_fk('backup_fk');
	}

	//------------------------------------------------------------
	public function drop_tables(){
		return $this->remove_fk().$this->exe_global('remove_tab');
	}//metodo
	//------------------------------------------------------------
	public function query_arquivos($tab){
		if(is_array($tab)){
			$relatorio='';
			foreach ($tab as $t) {
				if(file_exists($t)){
					if($this->query_arquivo($t)){
						$relatorio=$relatorio."Arquivo: (".$t.") executado com sucesso!\n";
					}else{
						$relatorio=$relatorio."Arquivo: (".$t.") Erro ".$this->erro."\n";
					}
				}else{
					$relatorio=$relatorio."Arquivo: (".$t.") Não existe!\n";
				}
			}
			return $relatorio;
		}else{
			return false;
		}
	}//metodo
	//-----------------------------------------------------------
	public function create_database(){
		if( $this->verifica_servidor() ){
			if( $this->query('create database '.$this->banco.' CHARACTER SET utf8 COLLATE utf8_bin') ){
				return $this->verifica_servidor(true);
			}else{
				$this->erro="Não foi pocivel criar seu banco de dados!";
				return false;
			}
		}else{
			return false;
		}
	}//metodo
	//----------------------------------------------------------
	public function set_arquivo($dir,$conteudo){
		$fp = fopen($dir, "w");
		fwrite($fp, $conteudo);
		fclose($fp);
	}
	//----------------------------------------------------------
	public function importa_backup($dir){
		$relatorio='';
		$this->remove_fk();
		/*$dao=$this->query_array('show tables');
		foreach ($dao as $d) {
			$this->query('truncate table '.$d['Tables_in_'.$this->banco]);
			$relatorio=$relatorio."Truncate ".$d['Tables_in_'.$this->banco]." Retorno: ".$this->erro;
		}*/
		$this->query_arquivo($dir);
		$relatorio=$relatorio."Arquivo ".$dir." Retorno: ".$this->erro;
		return $relatorio;
	}
	//-----------------------------------------------------------
	public function backup($dir){
		$re='';
		$script='';
		$dao=$this->query_array('show tables');
		foreach ($dao as $d) {
			$script=$script.
		  'INSERT INTO '.$d['Tables_in_'.$this->banco]."\n(";
		  $sql="show COLUMNS from ".$d['Tables_in_'.$this->banco];
		  $dao2=$this->query_array($sql);
			$tot=count($dao2)-1;
		  $dao3=$this->query_array("select * from ".$d['Tables_in_'.$this->banco]);

			$x2=0;
		  foreach ($dao2 as $d2) {
				if($x2==$tot){
					$script=$script.$d2['Field'];
				}else{
					$script=$script.$d2['Field'].",";
				}

				$x2++;
		  }
		  $script=$script.") VALUES\n";
		  $cor=0;
			$tot3=count($dao3)-1;
			$x3=0;
		  foreach ($dao3 as $d3) {

		    $script=$script."(";
				$x2=0;
		    foreach ($dao2 as $d2) {
					if($x2==$tot){
						$script=$script. "'".$d3[$d2['Field']]."'";
					}else{
						$script=$script. "'".$d3[$d2['Field']]."',";
					}
					$x2++;
		    }
				if($x3==$tot3){
					$script=$script.");\n";
				}else{
					$script=$script."),\n";
				}
				$x3++;
		  }
			if($tot3>0){
				$script=$script."\n";
				$re=$re.$script;
			}else{
				$script='';
			}

		}
		$re=$re."\n".$this->backup_fk();
		$this->set_arquivo($dir,$re);
		return $re;
	}
	//-----------------------------------------------------------
}//class


//alter table posts drop foreign key fk_posts;
/////////////////////////////////////////////////////////////////////////////////

//$con= new conexao();
//$con->query_arquivos('../import/db.mysql.sql');
//$con->create_database()
/*$con=new conexao(
  [
    "ip"=>"108.179.192.195",
    "banco"=>"tecxpc70_te cxp",
    "usuario"=>"tecxpc70_lcampos",
    "senha"=>"lu1cas23$$"
  ]
);
//$con->create_database();
$con->verifica_servidor(true);
$val=$con->query_array('show databases');
print_r($val);
echo "\nmsg:".$con->erro."\n";*/
?>
