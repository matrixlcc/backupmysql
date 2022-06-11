# backupmysql
Backup e manipulação de banco de dados MYSQL com PHP

<h2>Como utilizar:</h2>

<h3>Armazenar dados de conexão</h3>
<ul>
  <li>require 'backupmysql/conexao.php';</li>
  <li>$con=new conexao(
    <ul>
      <li>'conexao/cenexao.php',//diretorio de onde será armazenar os dados de conexão</li>
      <li>[
        <ul>
          <li>'ip'=>'localhost', //ip do seu banco</li>
          <li>'banco'=>'nome_bd', //nome da sua base de dados</li>
          <li>'usuario'=>'root', //usuário de acesso ao banco</li>
          <li>'senha'=>'' //senha do usuário</li>
        </ul>
      ]</li>
    </ul>
    );</li>

</ul>

<h3>Depois de armazenar os dados de conexão:</h3>
<ul>
  <li>require 'backupmysql/conexao.php';</li>
  <li>$con=new conexao(
    <ul>
      <li>'conexao/cenexao.php',//diretorio onde está armazenado os dados de conexao</li>
    </ul>
  );</li>
</ul>

<h3>Executar uma consulta sql e retornar uma lista:</h3>
<ul>
  <li>$con->query_array("select * from minha_tabela");</li>
</ul>

<h3>Executar uma inserção e retornar o id:</h3>
<ul>
  <li>$con->query_id("insert into minha_tabela values('valo1','valor2')");</li>
</ul>

<h3>Executar uma alteração e retorna true ou false:</h3>
<ul>
  <li>$con->query("update minha_tabela set coluna='valor' where coluna_id='id'");</li>
</ul>

<h3>Retornar erro do mysql:</h3>
<ul>
  <li>$con->erro;</li>
</ul>

<h3>Fazer backup do banco de dados:</h3>
<ul>
  <li>$con->backup('local_do_backup/backup.sql'));</li>
</ul>

<h3>Importar backup do banco de dados:</h3>
<ul>
  <li>$con->importar_backup('local_do_backup/backup.sql'));</li>
</ul>

<h3>Destruir tabelas:</h3>
<ul>
  <li>$con->drop_tables();</li>
</ul>

<h3>Executar arquivo sql:</h3>
<ul>
  <li>$con->query_arquivo('diretorio_arquivo/banco.sql');</li>
</ul>

<h3>Executar vários arquivos sql:</h3>
<ul>
  <li>$con->query_arquivos(['diretorio_arquivo/banco.sql','arquivo2.sql']);</li>
</ul>
