<html>
  <head>
  <title>sygnatury</title>

  <style type="text/css">
  		* {
			margin: 0;
			padding: 0;
		}
		body {
			font: 100% normal Arial, Helvetica, sans-serif;
			background: #161712;
		}
		form, input, select, textarea {
			margin:0;
			padding:0;
			color:#ffffff;
		}

		div.box {
			margin:auto;
			width:550px;
			background:#222222;
			position:relative;
			top:50px;
			border:1px solid #262626;
		}
		div.box h1 {
			color:#ffffff;
			font-size:18px;
			text-transform:uppercase;
			padding:5px 0 5px 5px;
			border-bottom:1px solid #161712;
			border-top:1px solid #161712;
			text-align: center;
		}

		div.box label {
			width:100%;
			display: block;
			background:#1C1C1C;
			border-top:1px solid #262626;
			border-bottom:1px solid #161712;
			padding:10px 0 10px 0;
		}

		div.box label span {
			display: block;
			color:#bbbbbb;
			font-size:12px;
			float:left;
			width:100px;
			text-align:right;
			padding:5px 20px 0 0;
		}

		div.box .wpis {
			padding:10px 10px;
			width:200px;
			background:#262626;
			border-bottom: 1px double #171717;
			border-top: 1px double #171717;
			border-left:1px double #333333;
			border-right:1px double #333333;
		}

		div.box .wiadomosc {
			padding:7px 7px;
			width:350px;
			background:#262626;
			border-bottom: 1px double #171717;
			border-top: 1px double #171717;
			border-left:1px double #333333;
			border-right:1px double #333333;
			overflow:hidden;
			height:150px;
		}

		div.box .button	{
			margin:0 0 10px 0;
			padding:4px 7px;
			background:#ff4800;
			border:0px;
			position: relative;
			top:10px;
			left:120px;
			width:100px;
			border-bottom: 1px double #d93d00;
			border-top: 1px double #d93d00;
			border-left:1px double #fe956c;
			border-right:1px double #fe956c;
		}
		.zdjecie {
			float: left;
		}
		.napisy_pod_zdjeciem {
			color: #838382;
			text-align:center;
		}
		.stopka {
			clear: both;
			color: #ffffff;
			padding-top: 50px;
			text-align:center;
			font-size: 12px;
		}
		a {
			text-decoration: none;
			color: #595959;
		}
		a:hover{
		   text-decoration: none;
		   color: #838382;
		}
		img {
			margin: 10px 10px 10px 10px;
			border-width: 5px;
			border-color: #333;
			border-style:solid;
		}

  </style>

  </head>
  <body>

	<form method="post" action="sygnatura4.php">
		<div class="box">
			<h1>wyszukiwarka zdjęć :</h1>
			<label>
				<span>sygnatura</span>
				<input type="text" class="wpis" name="sygnatura" value="<?php echo $_POST[sygnatura];?>" id="biuro"/>
			</label>

			<?php
				$tablica=explode('-',$_POST[sygnatura]);
				//$biuro = $tablica['0'];
				$biuro = preg_replace("/[^0-9]/","", $tablica['0']);
				if ($biuro < 100) { $biuro = substr($biuro,1); }
				//$numer = $tablica['1'];
				$numer = preg_replace("/[^0-9]/","", $tablica['1']);
				//print '<br>biuro nr: '.$biuro.'<br>numer oferty: '.$numer.', tablica[0]: '.$tablica['0'];
				//print '<br>$_POST[sygnatura]: '.$_POST[sygnatura].' i '.intval($_POST[sygnatura]);

            ?>

			<label>
				<span>adres:</span>
				<?php echo '<font color="white">http://inet.wgn.pl/oferty/',$biuro,'/',$numer,'/foto/',$numer,'-',$zdjecie,'x.jpg</font>'; ?>

			</label>
			<label>
				<input type="submit" class="button" value="Wyślij" />
			</label>
		</div>
	</form><br><br><br><br>

    	<?php

            for($i = 1; $i <= 40; $i++ ) {
      				$path = "http://inet.wgn.pl/oferty/$biuro/$numer/foto/$numer-$i.jpg";
					echo '<div class="zdjecie">';
					if (@fopen($path,'r')) {
						echo '<img src="http://inet.wgn.pl/oferty/',$biuro,'/',$numer,'/foto/',$numer,'-',$i,'.jpg" height="200">';
						echo '<br><div class="napisy_pod_zdjeciem">',$biuro,'-',$numer,'_',$i,'</div>';
					} else {
						echo '';
					};
					echo '</div>';
			};

        ?>

        <br>
		<h2 class="stopka">Powered by <a href="http://www.makos.net.pl">makos.net.pl</a></h2>


  </body>
 </html>

