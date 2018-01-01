<?php header('Content-type: application/xhtml+xml'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:svg="http://www.w3.org/2000/svg">
	<head>
		<title><?php echo "$boardtitle"; ?></title>
		<style>
table.main {
	border-spacing: 10px;
	top: 40%;
	width: 35em;
	font-family: Verdana;
	color: #FFffff;
	font-size: 12px;
	margin: auto;
	background-color: #C02020;
}
		</style>
	</head>
	<body style="background-color:#101020">
		<table class="main">
			<tr>
				<td style="vertical-align:middle;height:100px;width:50px">
					<svg:svg width="50" height="51" style="vertical-align:middle">
						<svg:circle cx="25" cy="26" r="25" fill="#FF6060" />
						<svg:line x1="13" y1="14" x2="37" y2="38" style="stroke:#C02020;stroke-width:5;" />
						<svg:line x1="37" y1="14" x2="13" y2="38" style="stroke:#C02020;stroke-width:5;" />
					</svg:svg>
				</td>
				<td>
					<table>
						<tr style="height:10px">
							<td><font face="Courier New,Courier,Mono" size="4"><b>I AM ERROR.</b></font></td>
						</tr>
						<tr>
							<td>
								Access to the board has been restricted by the administration.
								Please forgive any inconvenience caused and stand by until the underlying issues have been resolved.
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>