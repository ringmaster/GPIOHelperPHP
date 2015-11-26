<?php

include 'gpiohelper.php';

$io = new GPIO([1 => GPIO::OUT]);

if(isset($_GET['set'])) {
	$io->setPin($_GET['set'], 1);
}

if(isset($_GET['unset'])) {
	$io->setPin($_GET['unset'], 0);
}

if(isset($_GET['toggle'])) {
	$pinval = $io->getPin($_GET['toggle']);
	$newpinval = 1 - $pinval;
	$io->setPin($_GET['toggle'], $newpinval);
}

if(isset($_GET['direction'])) {
	if($io->getDirection($_GET['direction']) == GPIO::OUT) {
		$io->setDirection($_GET['direction'], GPIO::IN);
	}
	else {
		$io->setDirection($_GET['direction'], GPIO::OUT);
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
	<?php if($io->getDirection($pin) == GPIO::OUT): ?>
		<a href="?unset=<?= $pin ?>">Set 0</a>
	<?php endif; ?>
	</th>
	<th>
	<?php if($io->getDirection($pin) == GPIO::OUT): ?>
		<a href="?set=<?= $pin ?>">Set 1</a>
	<?php endif; ?>
	</th>
	<th>
	<?php if($io->getDirection($pin) == GPIO::OUT): ?>
		<a href="?toggle=<?= $pin ?>">Toggle</a>
	<?php endif; ?>
	</th>
</tr>
<?php endforeach; ?>
</tbody>

</body>
</html>
