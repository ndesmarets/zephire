<?php
echo "<h1>Modifier la progression</h1>";
if ( estprof() ) {
	$classe     = !empty( $_GET[ 'idcl' ] ) ? $_GET[ 'idcl' ] : NULL;
	$classe     = verifclasse( $link->real_escape_string( $classe ) );
	$discipline = !empty( $_GET[ 'idds' ] ) ? $_GET[ 'idds' ] : NULL;
	$discipline = verifdiscipline( $link->real_escape_string( $discipline ) );
	if ( !empty( $classe ) ) {
		$infocl    = infocl( $classe );
		$niveau    = $infocl[ 'niveau' ];
		$nomclasse = $infocl[ 'nom' ];
	}
	echo "<div class='selecteurs'><p>";
	selectclasse( "\"mdfprogressions.php?idcl=\" + this.value" );
	selectdiscipline( "\"mdfprogressions.php?idcl=$classe&idds=\" + this.value" );
	echo "</p></div>";
	if ( !empty( $classe ) && !empty( $discipline ) ) {
		if ( ( !empty( $_POST[ 'subChap' ] ) ) ) {
			if ( ( !empty( $_POST[ "id" ] ) ) ) {
				for ( $i = 1; $i <= sizeof( $_POST[ "id" ] ); $i++ ) {
					$numero         = formatid( $_POST[ 'numero' ][ $i ] );
					$exid           = $link->real_escape_string( $_POST[ 'id' ][ $i ] );
					$id             = $link->real_escape_string( $classe . "." . $discipline . "." . $numero );
					$nom            = $link->real_escape_string( $_POST[ 'titre' ][ $i ] );
					$competences    = $link->real_escape_string( $_POST[ 'competences' ][ $i ] );
					$baremes        = corrigebaremes( $competences, $link->real_escape_string( $_POST[ 'baremes' ][ $i ] ) );
					$nbevaluations  = $link->real_escape_string( $_POST[ 'nbevaluations' ][ $i ] );
					$autoevaluation = $link->real_escape_string( $_POST[ 'autoevaluation' ][ $i ] );
					$mode           = $link->real_escape_string( $_POST[ 'mode' ][ $i ] );
					$trimestre      = $link->real_escape_string( $_POST[ 'trimestre' ][ $i ] );
					$date           = $link->real_escape_string( envdate( $_POST[ 'date' ][ $i ] ) );
					$sql            = "UPDATE " . $prefix . "chapitres SET nom = '$nom', competences = '$competences', baremes = '$baremes', trimestre = $trimestre, date = '$date', nbevaluations = '$nbevaluations', autoevaluation = '$autoevaluation', mode = $mode, classe = '$classe', discipline = '$discipline', libelle = '$numero' WHERE id = '$exid'";
					$link->query( $sql );
					if ( $exid !== $id ) {
						updidchapitre( $exid, $id );
					}
				}
			}
			if ( !empty( $_POST[ 'numeronew' ] ) ) {
				$numero         = formatid( $_POST[ 'numeronew' ] );
				$id             = $link->real_escape_string( $classe . "." . $discipline . "." . $numero );
				$nom            = $link->real_escape_string( $_POST[ 'titrenew' ] );
				$competences    = $link->real_escape_string( $_POST[ 'competencesnew' ] );
				$baremes        = corrigebaremes( $competences, $link->real_escape_string( $_POST[ 'baremesnew' ] ) );
				$nbevaluations  = $link->real_escape_string( $_POST[ 'nbevaluationsnew' ] );
				$autoevaluation = $link->real_escape_string( $_POST[ 'autoevaluationnew' ] );
				$mode           = $link->real_escape_string( $_POST[ 'modenew' ] );
				$trimestre      = $link->real_escape_string( $_POST[ 'trimestrenew' ] );
				$date           = $link->real_escape_string( envdate( $_POST[ 'datenew' ] ) );
				$sql            = "INSERT INTO " . $prefix . "chapitres (id, nom, competences, baremes, trimestre, date, nbevaluations, autoevaluation, mode, classe, discipline, libelle) VALUES ('$id', '$nom', '$competences', '$baremes', $trimestre, '$date', '$nbevaluations', '$autoevaluation', $mode, '$classe', '$discipline', '$numero')";
				$link->query( $sql );
			}
		}
		if ( ( !empty( $_POST[ 'subSupprChap' ] ) ) && ( !empty( $_POST[ 'supprch' ] ) ) ) {
			$supprch = $link->real_escape_string( $_POST[ 'supprch' ] );
			updidchapitre( $supprch, "" );
		}
		if ( !empty( $_POST[ 'subOpt' ] ) ) {
			$lstnotes = "";
			for ( $i = 0; $i < sizeof( $_POST[ 'noteso' ] ); $i++ ) {
				if ( $i !== 0 ) {
					$lstnotes .= "|";
				}
				$lstnotes .= $_POST[ 'noteso' ][ $i ];
			}
			$lstlibs = "";
			for ( $i = 0; $i < sizeof( $_POST[ 'libso' ] ); $i++ ) {
				if ( $i !== 0 ) {
					$lstlibs .= "|";
				}
				$lstlibs .= $_POST[ 'libso' ][ $i ];
			}
			$lstdescs = "";
			for ( $i = 0; $i < sizeof( $_POST[ 'descso' ] ); $i++ ) {
				if ( $i !== 0 ) {
					$lstdescs .= "|";
				}
				$lstdescs .= $_POST[ 'descso' ][ $i ];
			}
			$lstnotes = $link->real_escape_string( $lstnotes );
			$lstlibs  = $link->real_escape_string( $lstlibs );
			$lstdescs = $link->real_escape_string( $lstdescs );
			$nompacko = $link->real_escape_string( $_POST[ 'nompacko' ] );
			$result   = $link->query( "SELECT * FROM " . $prefix . "notations WHERE classe = '$classe' AND discipline = '$discipline'" );
			if ( $result->num_rows == 0 ) {
				$sql = "INSERT INTO " . $prefix . "notations (id, libelles, descriptions, notations, icones, classe, discipline) VALUES ('$classe.$discipline', '$lstlibs', '$lstdescs', '$lstnotes', '$nompacko', '$classe', '$discipline')";
			} else {
				$sql = "UPDATE " . $prefix . "notations SET libelles = \"$lstlibs\", descriptions = \"$lstdescs\", notations = \"$lstnotes\", icones = \"$nompacko\", classe = \"$classe\", discipline = \"$discipline\" WHERE id = '$classe.$discipline'";
			}
			$link->query( $sql );
		}
		if ( ( !empty( $_POST[ 'subUpload' ] ) ) && ( !empty( $_FILES[ 'fichiercsv' ][ 'tmp_name' ] ) ) ) {
			$result = $link->query( "SELECT * FROM " . $prefix . "chapitres WHERE classe = '$classe' AND discipline = '$discipline'" );
			while ( $r = mysqli_fetch_array( $result ) ) {
				updidchapitre( $r[ 'id' ], "" );
			}
			$fichiercsv = $_FILES[ 'fichiercsv' ][ 'tmp_name' ];
			$fic        = fopen( "$fichiercsv", 'rb' );
			$first      = true;
			while ( $ligne = fgetcsv( $fic, 1024, ";", '"') ) {			
				$symbole = substr( $ligne[ 0 ], 0, 1 );
				if ( $symbole !== "#" ) {
					if ( $first ) {
						$lstlibs  = $link->real_escape_string( $ligne[ 0 ] );
						$lstdescs = $link->real_escape_string( $ligne[ 1 ] );
						$lstnotes = $link->real_escape_string( $ligne[ 2 ] );
						$nompacko = $link->real_escape_string( $ligne[ 3 ] );
						$result   = $link->query( "SELECT * FROM " . $prefix . "notations WHERE classe = '$classe' AND discipline = '$discipline'" );
						if ( $result->num_rows == 0 ) {
							$sql = "INSERT INTO " . $prefix . "notations (id, libelles, descriptions, notations, icones, classe, discipline) VALUES ('$classe.$discipline', '$lstlibs', '$lstdescs', '$lstnotes', '$nompacko', '$classe', '$discipline')";
						} else {
							$sql = "UPDATE " . $prefix . "notations SET libelles = \"$lstlibs\", descriptions = \"$lstdescs\", notations = \"$lstnotes\", icones = \"$nompacko\", classe = \"$classe\", discipline = \"$discipline\" WHERE id = '$classe.$discipline'";
						}
						$link->query( $sql );
						$first = false;
					} else {
						$numero         = preg_replace( "[^a-zA-Z0-9\ ]", "", $ligne[ 0 ] );
						$id             = $link->real_escape_string( $classe . "." . $discipline . "." . $numero );
						$nom            = $link->real_escape_string( $ligne[ 1 ] );
						$competences    = $link->real_escape_string( $ligne[ 2 ] );
						$baremes        = corrigebaremes( $competences, $link->real_escape_string( $ligne[ 3 ] ) );
						$ceintures      = $link->real_escape_string( $ligne[ 4 ] );
						$nbevaluations  = $link->real_escape_string( $ligne[ 5 ] );
						$autoevaluation = $link->real_escape_string( $ligne[ 6 ] );
						$mode           = $link->real_escape_string( $ligne[ 7 ] );
						$trimestre      = $link->real_escape_string( $ligne[ 8 ] );
						$date           = $link->real_escape_string( envdate( $ligne[ 9 ] ) );
						$sql            = "INSERT INTO " . $prefix . "chapitres (id, nom, competences, baremes, nbevaluations, autoevaluation, mode, trimestre, date, classe, discipline, libelle) VALUES ('$id', '$nom', '$competences', '$baremes', '$nbevaluations', '$autoevaluation', $mode, $trimestre, '$date', '$classe', '$discipline', '$numero')";
					}
					$link->query( $sql );
				}
			}
			fclose( $fic );
		}
		echo "<h2>Chapitres</h2>";
		echo "<form action='mdfprogressions.php?idcl=" . $classe . "&idds=" . $discipline . "' method='POST'><table class='data large'><thead><tr><th style='width:40px;'>Num</th><th>Titre</th><th>Comp&eacute;tences</th><th>Bar&egrave;mes / Rep&egrave;res</th><th style='width:60px;'>Nb evals</th><th style='width:60px;'>Autoeval</th><th style='width:60px;'>Mode</th><th style='width:60px;'>Trimestre</th><th style='width:100px;'>Date</th></tr></thead><tbody>";
		$result = $link->query( "SELECT * FROM " . $prefix . "chapitres WHERE classe = '$classe' AND discipline = '$discipline' ORDER BY id" );
		$i      = 1;
		while ( $r = mysqli_fetch_array( $result ) ) {
			$id             = $r[ 'id' ];
			$numero         = substr( $r[ 'id' ], strlen( $classe ) + strlen( $discipline ) + 2 );
			$titre          = $r[ 'nom' ];
			$competences    = $r[ 'competences' ];
			$baremes        = $r[ 'baremes' ];
			$nbevaluations  = $r[ 'nbevaluations' ];
			$autoevaluation = $r[ 'autoevaluation' ];
			$mode           = $r[ 'mode' ];
			$trimestre      = $r[ 'trimestre' ];
			$date           = $r[ 'date' ];
			echo "<tr>";
			echo "<td><input type='hidden' value=\"$id\" name='id[$i]' /><input type='text' class='inputcell' value=\"$numero\" name='numero[$i]' /></td>";
			echo "<td><input type='text' class='inputcell' value=\"$titre\" name='titre[$i]' /></td>";
			echo "<td style='text-align:left;'><input type='text' class='inputcell' value=\"$competences\" name='competences[$i]' /><br /><a href='selectcompetences.php?idcl=$classe&idds=$discipline&idch=$numero'>Sélectionner...</a></td>";
			if ( $mode == 1 ) {
				//echo "<td><input type='hidden' class='inputcell' value=\"$baremes\" name='baremes[$i]' /><a href='#' onclick='openModal(\"mdfceintures.php?idcl=$classe&idds=$discipline&idch=$id\");'>Modifier descriptifs</a></td>";
				echo "<td style='text-align:left;'><input type='hidden' class='inputcell' value=\"$baremes\" name='baremes[$i]' /><a href='mdfreperes.php?idcl=$classe&idds=$discipline&idch=$numero'>Modifier Rep&egrave;res...</a></td>";
			} else {
				echo "<td style='text-align:left;'><input type='text' class='inputcell' value=\"$baremes\" name='baremes[$i]' /><br /><a href='mdfbaremes.php?idcl=$classe&idds=$discipline&idch=$numero'>Modifier Barèmes...</a></td>";
			}
			echo "<td><select name='nbevaluations[$i]'>";
			for ( $k = 1; $k <= 10; $k++ ) {
				echo "<option value=$k";
				if ( $k == $nbevaluations ) {
					echo " selected";
				}
				echo ">$k</option>";
			}
			echo "</select></td>";
			echo "<td><select name='autoevaluation[$i]'>";
			$booleen = array(
				 "Non",
				"Oui" 
			);
			foreach ( $booleen as $c => $v ) {
				echo "<option value=$c";
				if ( $c == $autoevaluation ) {
					echo " selected";
				}
				echo ">$v</option>";
			}
			echo "</select></td>";
			echo "<td><select name='mode[$i]'>";
			echo "<option value=0";
			if ( $mode == 0 ) {
				echo " selected";
			}
			echo ">Traditionnel</option>";
			echo "<option value=1";
			if ( $mode == 1 ) {
				echo " selected";
			}
			echo ">Ceintures</option>";
			echo "<option value=2";
			if ( $mode == 2 ) {
				echo " selected";
			}
			echo ">Notes</option>";
			echo "</select></td>";
			echo "<td><select name='trimestre[$i]'><option value=0>...</option>";
			for ( $k = 1; $k <= 3; $k++ ) {
				echo "<option value=$k";
				if ( $k == $trimestre ) {
					echo " selected";
				}
				echo ">$k</option>";
			}
			echo "</select></td>";
			echo "<td><input type='date' class='inputcell' value=\"" . affdate( $date ) . "\" name='date[$i]' /></td>";
			echo "</tr>";
			$i++;
		}
		echo "<tr>";
		echo "<td><input type='text' class='inputcell' value='' name='numeronew' placeholder='...' /></td>";
		echo "<td><input type='text' class='inputcell' value='' name='titrenew' placeholder='...' /></td>";
		echo "<td><input type='text' class='inputcell' value='' name='competencesnew' placeholder='...' /></td>";
		echo "<td><input type='text' class='inputcell' value='' name='baremesnew' placeholder='...' /></td>";
		echo "<td><select name='nbevaluationsnew'><option value=1>1</option><option value=2>2</option><option value=3>3</option><option value=4>4</option><option value=5>5</option><option value=6>6</option><option value=7>7</option><option value=8>8</option><option value=9>9</option><option value=10>10</option></select></td>";
		echo "<td><select name='autoevaluationnew'><option value=1>Oui</option><option value=0>Non</option></select></td>";
		echo "<td><select name='modenew'><option value=0>Traditionnel</option><option value=1>Ceintures</option><option value=2>Notes</option></select></td>";
		echo "<td><select name='trimestrenew'><option value=0>...</option><option value=1>1</option><option value=2>2</option><option value=3>3</option></select>";
		echo "<td><input type='date' class='inputcell' value='' name='datenew' placeholder='../../....' /></td>";
		echo "</tr></tbody></table>";
		echo "<p class='noprint'><input type='submit' value='Valider' name='subChap' /></p></form>";
		$result = $link->query( "SELECT * FROM " . $prefix . "chapitres WHERE classe = '$classe' AND discipline = '$discipline' ORDER BY id" );
		echo "<form action='mdfprogressions.php?idcl=" . $classe . "&idds=" . $discipline . "' name='formsuppr' method='POST' class='noprint'><p>Supprimer un chapitre : <select name='supprch'><option value='' selected>...</option>";
		while ( $r = mysqli_fetch_array( $result ) ) {
			$idch  = $r[ 'id' ];
			$nomch = $r[ 'nom' ];
			echo "<option value='$idch'>" . substr( $idch, strlen( $classe ) + strlen( $discipline ) + 2 ) . ") $nomch</option>";
		}
		echo "</select> <input type='submit' value='Valider' name='subSupprChap' /></p></form>";
		echo "<h2>Options</h2>";
		echo "<form action='mdfprogressions.php?idcl=" . $classe . "&idds=" . $discipline . "' method='POST'>";
		echo "<p>Je personnalise les comp&eacute;tences :</p>";
		echo "<table class='data large'><thead><tr><th>Code</th><th style='width:60px;'>Libell&eacute;</th><th>Description</th><th style='width:60px;'>Pourcentage de la note</th></tr></thead><tbody>";
		$libs     = array(
			'',
			'',
			'',
			'' 
		);
		$descs    = array(
			'',
			'',
			'',
			'' 
		);
		$codes   = array('NA', 'ECA', 'PA', 'A' );
		$notes = donnenota( $classe, $discipline );
		$libs = donnelib( $classe, $discipline, 0 );
		$descs = donnedesc( $classe, $discipline );
		$packact = $r[ 'icones' ];
		if($packact == ""){
			$packact = "Origico";
		}
		for ( $i = 0; $i < 4; $i++ ) {
			echo "<tr>";
			echo "<td style='width:40px;'>".$codes[ $i ]."</td>";
			echo "<td><input type='text' class='inputcell' value=\"".$libs[ $codes[$i] ]."\" name='libso[$i]' /></td>";
			echo "<td><input style='width:100%;' type='text' class='inputcell' value=\"".$descs[ $codes[$i] ]."\" name='descso[$i]' /></td>";
			echo "<td><input type='text' class='inputcell' value='".$notes[ $codes[$i] ]."' name='noteso[$i]' /></td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
		echo "<p>Je choisis le pack d'ic&ocirc;nes suivant :</p>";
		echo "<table class='data large'><thead><tr><th>Nom du pack</th><th>Non Evalu&eacute;</th><th>NA</th><th>ECA</th><th>PA</th><th>A</th><th>En attente</th><th>Valid&eacute;</th><th>Non valid&eacute;</th></tr></thead><tbody>";
		$packicones = listedossiers( "images/Icones" );
		foreach ( $packicones as $pico ) {
			$tabpico = explode( "/", $pico );
			$nompico = $tabpico[ sizeof( $tabpico ) - 1 ];
			echo "<tr><td><input type='radio' name='nompacko' value='" . $nompico . "'";
			if ( $packact == $nompico ) {
				echo " checked";
			}
			echo "/> " . $nompico . "</td>";
			$lstcodes = array("", "NA", "ECA", "PA", "A", "ATT", "OUI", "NON");
			foreach ( $lstcodes as $uncode ) {
				if ( $uncode !== "" ) {
					$icone = $dir . "images/Icones/$nompico/$uncode.png";
				} else {
					$icone = $dir . "images/Icones/$nompico/NE.png";
				}
				if ( file_exists( $icone ) ) {
					echo "<td><img alt='$uncode' src='$icone' /></td>";
				} else {
					echo "<td>" . $libs[ $uncode ] . "</td>";
				}
			}
			echo "</tr>";
		}
		echo "</tbody></table>";
		echo "<p class='noprint'><input type='submit' value='Valider' name='subOpt' /></p></form>";
		echo "<h2>Importation et exportation</h2>";
		echo "<form action='mdfprogressions.php?idcl=" . $classe . "&idds=" . $discipline . "' name='formupload' enctype='multipart/form-data' method='POST' class='noprint'><p>Importer un fichier CSV : <input type='file' name='fichiercsv' />";
		echo "<input type='submit' value='Valider' name='subUpload' /></p></form>";
		echo "<p class='noprint'>Exporter un fichier CSV : <input type='submit' value='T&eacute;l&eacute;charger' onclick='location.href=\"exportcsv.php?cont=progressions&amp;idcl=" . $classe . "&amp;idds=" . $discipline . "\"' name='subExport' /></p>";
	} else {
		echo "<p>Vous n'avez pas choisi soit la classe, soit la discipline.</p>";
	}
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute;.</p>";
}
?>