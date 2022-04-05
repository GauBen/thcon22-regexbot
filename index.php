<?php

const MAZE = [
  '#############',
  '#   #   #   #',
  '# # # # # ###',
  '#     #   # #',
  '#####   #   #',
  '#   ### # ###',
  '###         #',
  '#   # # ### #',
  '# ### ###   #',
  '#  #      # #',
  '# ## #### ###',
  '#       #   #',
  '#############'
];
const SIZE = 13;

const UP = 0;
const LEFT = 1;
const DOWN = 2;
const RIGHT = 3;

function sight(int $x, int $y, int $dir): string
{
  $str = '';
  $i = $dir & 1 ? $x : $y;
  $max = $dir & 2 ? SIZE : -1;
  $inc = $dir & 2 ? 1 : -1;
  while ($i != $max) {
    $str .= MAZE[$dir & 1 ? $y : $i][$dir & 1 ? $i : $x];
    $i += $inc;
  }
  return substr($str, 1);
}

function run(string $regex): array
{
  $x = 1;
  $y = 1;
  $dir = RIGHT;
  $output = [];

  $i = 100;
  while ($i > 0) {
    if (MAZE[$y][$x] != ' ') {
      $output[] = 'Your bot hit a wall!';
      $output[] = [$x, $y, $dir];
      return $output;
    } else if ($x == SIZE - 2 && $y == SIZE - 2) {
      $output[] = 'Well done! Here is the flag: ' . file_get_contents('./flag.txt');
      $output[] = [$x, $y, $dir];
      return $output;
    }
    $sight = sight($x, $y, $dir);
    $output[] = sprintf("At (%2d, %2d) facing %5s, seeing '%s'", $x, $y, ['up', 'left', 'down', 'right'][$dir], $sight);
    if (@preg_match($regex, $sight)) {
      if ($dir == UP) $y -= 1;
      if ($dir == DOWN) $y += 1;
      if ($dir == LEFT) $x -= 1;
      if ($dir == RIGHT) $x += 1;
    } else {
      $dir = ($dir + 1) & 3;
    }
    if (preg_last_error() !== PREG_NO_ERROR) {
      $output[] = 'Invalid regex...';
      $output[] = [$x, $y, $dir];
      return $output;
    }
    $i--;
  }

  $output[] = 'You are out of moves!';
  $output[] = [$x, $y, $dir];
  return $output;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Regex Bot</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
  <style>
    table {
      border-collapse: collapse;
      line-height: 1;
    }

    td {
      width: 1.5em;
      height: 1.5em;
    }

    .black {
      color: #fff;
      background-color: #000;
    }

    .red {
      color: #fff;
      background-color: #800;
    }
  </style>
</head>

<body class="container is-max-desktop p-5">
  <h1 class="block title is-1">Regex Bot</h1>
  <p class="block">You are controlling a small bot stuck in a maze. For strange reasons we can't explain, the only way to control the bot is by using a regular expression. If the regex matches, the bot move forward one square. Otherwise, it makes a quarter turn counter-clockwise. The regex is matched against the string that extends from the bot to the border wall. The bot starts in the top left corner (1, 1) and the exit is at the bottom right (11, 11). It can make 100 moves before shutting down.</p>
  <p class="block">Here is the maze:</p>
  <pre class="block" style="line-height: 0.8"><?= implode("\n", MAZE) ?></pre>
  <h2 class="title is-2">Run the bot</h2>
  <?php

  $bot = [1, 1, RIGHT];

  if (isset($_POST['regex'])) {
    $output = run($_POST['regex']);
    $bot = array_pop($output);
  }

  echo '<table class="block">' . PHP_EOL;
  foreach (MAZE as $y => $row) {
    echo '<tr>' . PHP_EOL;
    foreach (str_split($row) as $x => $char) {
      echo '<td'
        . ($char != ' ' ?
          ' class=' . ($bot[0] == $x && $bot[1] == $y ? 'red' : 'black')
          : '')
        . ' title="(' . $x . ', ' . $y . ')">'
        . (($bot[0] == $x && $bot[1] == $y) ? ['🔼', '◀️', '🔽', '▶️'][$bot[2]] : $char)
        . '</td>' . PHP_EOL;
    }
    echo '</tr>' . PHP_EOL;
  }
  echo '</table>';

  if (isset($output)) {
    echo '<pre class="block">' . implode(PHP_EOL, $output) .  '</pre>';
  }

  ?>
  <form method="POST" class="block">
    <p class="field control">
      <label class="label">
        Regex
        <input type="text" name="regex" class="input">
      </label>
    </p>
    <p class="field is-grouped is-grouped-centered">
      <button class="button is-link">Run</button>
    </p>
  </form>
</body>

</html>
