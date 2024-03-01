<?php

function redimensionar_imagem($image_path, $max_size) {
    // Carrega a imagem
    $img = imagecreatefromjpeg($image_path);
    $largura = imagesx($img);
    $altura = imagesy($img);

    // Calcula a proporção da imagem
    $proporcao = $largura / $altura;

    // Redimensiona a imagem
    if ($largura > $altura) {
        $nova_largura = $max_size;
        $nova_altura = intval($nova_largura / $proporcao);
    } else {
        $nova_altura = $max_size;
        $nova_largura = intval($nova_altura * $proporcao);
    }

    // Cria uma nova imagem com o novo tamanho
    $img_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);
    imagecopyresampled($img_redimensionada, $img, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura, $altura);

    return $img_redimensionada;
}

function criar_quadrado_com_imagem($image_path, $tamanho_quadrado) {
    // Redimensiona a imagem
    $img = redimensionar_imagem($image_path, $tamanho_quadrado);

    // Cria um quadrado branco
    $quadrado_branco = imagecreatetruecolor($tamanho_quadrado, $tamanho_quadrado);
    $cor_branca = imagecolorallocate($quadrado_branco, 255, 255, 255);
    imagefill($quadrado_branco, 0, 0, $cor_branca);

    // Calcula as coordenadas para centralizar a imagem no quadrado
    $x = ($tamanho_quadrado - imagesx($img)) / 2;
    $y = ($tamanho_quadrado - imagesy($img)) / 2;

    // Copia a imagem redimensionada para o quadrado branco
    imagecopy($quadrado_branco, $img, $x, $y, 0, 0, imagesx($img), imagesy($img));
  
    // Obtém o diretório e o nome do arquivo original
    $diretorio_origem = dirname($image_path);
    $nome_arquivo = pathinfo($image_path, PATHINFO_FILENAME);

    // Salva o quadrado com o mesmo nome e no mesmo diretório da imagem original
    $caminho_destino = $diretorio_origem . '/' . $nome_arquivo . '.jpg';
    imagejpeg($quadrado_branco, $caminho_destino);

    echo "Quadrado com imagem $image_path criado com sucesso!\n";
}

function processar_diretorio($diretorio, $tamanho) {
    // Lista todos os arquivos e diretórios no diretório atual
    $arquivos = glob($diretorio . '/*');

    // Verifica cada arquivo/diretório
    foreach ($arquivos as $arquivo) {
        // Ignora os diretórios especiais
        if ($arquivo == '.' || $arquivo == '..') {
            continue;
        }

        // Se for um diretório, processa-o recursivamente
        if (is_dir($arquivo)) {
            processar_diretorio($arquivo, $tamanho);
        } else {
            // Se for um arquivo, verifica se é uma imagem e cria o quadrado
            $extensoes_validas = array('jpg', 'jpeg', 'png', 'gif');
            $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
            if (in_array($extensao, $extensoes_validas)) {
                criar_quadrado_com_imagem($arquivo, $tamanho);
            } else {
                echo "O arquivo $arquivo não é uma imagem válida. Pulando...\n";
            }
        }
    }
}

// Verifica se o número correto de argumentos foi passado
if ($argc != 3) {
    echo "Uso: php criar_quadrado_com_imagens.php <diretório> <tamanho>\n";
    exit(1);
}

$diretorio = $argv[1];
$tamanho = $argv[2];

// Exemplo de uso:
processar_diretorio($diretorio, $tamanho);

?>