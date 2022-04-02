<?php
include '../conexao.php';
session_start();
$tipoUsuario = $_SESSION['tipoUsuario'];

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

$searchArray = array();

## Search 
$searchQuery = " ";
if($searchValue != ''){
	$searchQuery = " AND (cod_cliente LIKE :cod_cliente OR pedido LIKE :pedido OR fantasia LIKE :fantasia OR razao_social LIKE :razao_social OR cod_rca LIKE :cod_rca OR nome_rca LIKE :nome_rca OR cod_sup LIKE :cod_sup OR nome_sup LIKE :nome_sup ) ";
    $searchArray = array( 
        'cod_cliente'=>"%$searchValue%", 
        'pedido'=>"%$searchValue%",
        'fantasia'=>"%$searchValue%",
        'razao_social'=>"%$searchValue%",
        'cod_rca'=>"%$searchValue%",
        'nome_rca'=>"%$searchValue%",
        'cod_sup'=>"%$searchValue%",
        'nome_sup'=>"%$searchValue%"
    );
}

## Total number of records without filtering
$stmt = $db->prepare("SELECT COUNT(*) AS allcount FROM troca WHERE analise_apoio = 'Autorizado' AND POSICAO = 'B'");
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

## Total number of records with filtering
$stmt = $db->prepare("SELECT COUNT(*) AS allcount FROM troca WHERE 1 AND analise_apoio = 'Autorizado' AND POSICAO = 'B' ".$searchQuery);
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$stmt = $db->prepare("SELECT * FROM troca WHERE 1 ".$searchQuery." AND analise_apoio = 'Autorizado' AND POSICAO = 'B'  ORDER BY ".$columnName." ".$columnSortOrder." LIMIT :limit,:offset");

// Bind values
foreach($searchArray as $key=>$search){
    $stmt->bindValue(':'.$key, $search,PDO::PARAM_STR);
}

$stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
$stmt->execute();
$empRecords = $stmt->fetchAll();

$data = array();

foreach($empRecords as $row){

    $data[] = array(
        "pedido"=>$row['pedido'],
        "cod_cliente"=>$row['cod_cliente'],
        "fantasia"=>utf8_encode(utf8_decode($row['fantasia'])),
        "razao_social"=>utf8_encode(utf8_decode($row['razao_social'])),
        "data_troca"=>$row['data_troca'],
        "cod_rca"=>$row['cod_rca'] . " - " . utf8_encode(utf8_decode($row['nome_rca'])) ,
        "cod_sup"=>$row['cod_sup']. " - " . utf8_encode(utf8_decode($row['nome_sup'])),
        "valor_atend"=>"R$".str_replace(".",",",$row['valor_atend']) ,
        "analise_apoio"=>utf8_encode(utf8_decode($row['analise_apoio'])),
        "posicao"=>$row['posicao'],
        "acoes"=> '<a href="detalhes.php?idtroca='.$row['idtroca'].'" data-id="'.$row['idtroca'].'"  class="btn btn-info btn-sm editbtn" >Visualizar</a>'
    );
}

## Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);
