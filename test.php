<?php

include 'gpiohelper.php';

$io = new GPIOHelper([1 => GPIOHelper::OUT]);

if(isset($_GET['set'])) {
	$io->setPin($_GET['set'], 1);	
}

if(isset($_GET['unset'])) {
	$io->setPin($_GET['unset'], 0);	
}

if(isset($_GET['toggle'])) {
	$io->setPin($_GET['toggle'], 1 - $io->getPin($_GET['toggle']));
}

if(isset($_GET['direction'])) {
	if($io->getDirection($_GET['direction']) == GPIOHelper::OUT) {
		$io->setDirection($_GET['direction'], GPIOHelper::IN);
	}
	else {
		$io->setDirection($_GET['direction'], GPIOHelper::OUT);
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>GPIO PHP Test Page</title>
</head>
<body>

<table>
<thead><tr><th>Pin</th><th>Direction</th><th>Status</th><th>Set 0</th><th>Set 1</th><th>Toggle</th></tr></thead>

<tbody>
<?php foreach($io->getPins() as $pin): ?>
<tr>
	<th><?= $pin ?></th>
	<th><a href="?direction=<?= $pin ?>"><?= $io->getDirection($pin) ?></a></th>
	<th><?= $io->getPin($pin) ?></th>
	<th>
	<?php if($io->getDirection($pin) == GPIOHelper::OUT): ?>
		<a href="?unset=<?= $pin ?>">Set 0</a>
	<?php endif; ?>
	</th>
	<th>
	<?php if($io->getDirection($pin) == GPIOHelper::OUT): ?>
		<a href="?set=<?= $pin ?>">Set 1</a>
	<?php endif; ?>
	</th>
	<th>
	<?php if($io->getDirection($pin) == GPIOHelper::OUT): ?>
		<a href="?toggle=<?= $pin ?>">Toggle</a>
	<?php endif; ?>
	</th>
</tr>
<?php endforeach; ?>
</tbody>

</body>
</html>
