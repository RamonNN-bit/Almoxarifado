<h1>Itens</h1>
<?php
     $sql = "SELECT * FROM itens";

     $res = $conn->query($sql);

      $qtd = $res->num_rows;

      if ($qtd > 0 ) {
           print "<table>";
            while($row = $res->fetch_object()){
                print "<tr>";
                print "<td>".$row->id."</td>";
                print "<td>".$row->nome."</td>";
                print "<td>".$row->quantidade."</td>";
                print "<td>".$row->unidade."</td>";
                print "<td>".$row->marca."</td>";
                
                
                
                print $row ->unidade;
                print $row ->marca;
          print "</table>";
              
            }

      } else {
          print "Nenhum dado encontrado";
      }
?>
