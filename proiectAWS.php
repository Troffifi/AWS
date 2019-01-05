<!DOCTYPE html>
<html>
	<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
		<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

		<script type="text/javascript">
		$(document).ready(function() {
		    $('#example').DataTable( {
		        "pagingType": "full_numbers"
		    } );
		} );
		</script>

		<style type="text/css">
			.button {
			  background-color: #555555;
			  border: none;
			  color: white;
			  padding: 15px 32px;
			  margin: 5px;
			  text-align: center;
			  text-decoration: none;
			  display: inline-block;
			  font-size: 16px;
			  border-radius: 15%;
			  cursor: pointer;
			}

			form {
				display: inline-block;
			}
		</style>


	</head>  
	<body style="text-align: center;">
		<h1>Medicamente eliberate doar pe baza de prescriptie</h1>

		<form action="proiectAWS.php" method="post">
			<input type="text" name="name" placeholder="Search drug"><br>
			<input type="submit" value="Submit" class="button">
		</form>

		<div>
			<form action="proiectAWS.php" method="post">
				<input type="hidden" name="all" value="all" />
				<input type="submit" value="All drugs" class="button">
			</form>

			<form action="proiectAWS.php" method="post">
				<input type="hidden" name="all_prescribed" value="all_prescribed" />
				<input type="submit" value="All prescribed drugs" class="button">
			</form>

			<form action="proiectAWS.php" method="post">
				<input type="hidden" name="home" value="home" />
				<input type="submit" value="Home" class="button">
			</form>
		</div>

	</body>
</html>

<?php
if (isset($_POST['home'])) {
	header('Location: http://localhost/proiectAWS.php');
	unset($_POST['home']);
}


if (isset($_POST['name']) || isset($_POST['all']) || isset($_POST['all_prescribed'])) {
	require_once( "sparqllib.php" );

	$db = sparql_connect( "http://localhost:3030/DINTO-modified/sparql" );
	if( !$db ) {"connect - " . print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	
	if  (isset($_POST['name'])) {
		$sparql = "prefix owl: <http://www.w3.org/2002/07/owl#>
		PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
		prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		prefix dinto: <http://purl.obolibrary.org/obo/DINTO.owl>
		prefix is_pr: <http://purl.obolibrary.org/obo/DINTO.owl/is_prescribed>
		SELECT DISTINCT ?label ?is_prescribed
		WHERE {
		  ?class rdfs:label ?label.
		  ?class is_pr: ?is_prescribed
		  FILTER(?label = '" . $_POST['name'] . "')
		}";
		unset($_POST['name']);
	}
	if (isset($_POST['all'])) {
		$sparql = "prefix owl: <http://www.w3.org/2002/07/owl#>
		prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		prefix dinto: <http://purl.obolibrary.org/obo/DINTO.owl>
		prefix is_pr: <http://purl.obolibrary.org/obo/DINTO.owl/is_prescribed>
		SELECT DISTINCT ?label ?is_prescribed
		WHERE {
		  ?class rdfs:label ?label.
		  ?class is_pr: ?is_prescribed
		}";
		unset($_POST['all']);
	}
	if (isset($_POST['all_prescribed'])) {
		$sparql = "prefix owl: <http://www.w3.org/2002/07/owl#>
		PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
		prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		prefix dinto: <http://purl.obolibrary.org/obo/DINTO.owl>
		prefix is_pr: <http://purl.obolibrary.org/obo/DINTO.owl/is_prescribed>
		SELECT DISTINCT ?label ?is_prescribed
		WHERE {
		  ?class rdfs:label ?label.
		  ?class is_pr: ?is_prescribed
		  FILTER(?is_prescribed = 'true'^^xsd:boolean)
		}";
		unset($_POST['all_prescribed']);
	}



	$result = sparql_query( $sparql );
	if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	 
	$fields = sparql_field_array( $result );
	 
	print "<p>Number of rows: ".sparql_num_rows( $result )." results.</p>";
	print "<table id='example'>";
	print "<thead>";
	foreach( $fields as $field )
	{
		print "<th>$field</th>";
	}
	print "</thead>";
	print "<tbody>";
	while( $row = sparql_fetch_array( $result ) )
	{
		print "<tr>";
		foreach( $fields as $field )
		{
			print "<td>$row[$field]</td>";
		}
		print "</tr>";
	}
	print "</tbody>";
	print "</table>";
}
?>

