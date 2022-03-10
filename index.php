<?php

const MAZE = [
  '#C###########',
  'D   #   A   #',
  '# # # # # ###',
  '#     #   # #',
  '#B###   #   #',
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

function sight(int $x, int $y, int $dir): string {
  $str = '';
  $i = $dir & 1 ? $x : $y;
  $max = $dir & 2 ? SIZE - 1 : -1;
  $inc = $dir & 2 ? 1 : -1;
  while ($i != $max) {
    $str .= MAZE[$dir & 1 ? $y : $i][$dir & 1 ? $i : $x];
    $i += $inc;
  }
  return $str;
}

function run(string $regex) {
  $x = 1;
  $y = 1;
  $dir = RIGHT;

  $i = 100;
  while ($i > 0) {
    $sight = sight($x, $y, $dir);
    echo '(' . $x . ', ' . $y . ') ' . $dir . ' <code>' . $sight . '</code><br>' . PHP_EOL;
    if (MAZE[$y][$x] != ' ') {
      echo 'Position invalide<br>';
      return;
    }
    if (preg_match($regex, $sight)) {
      if ($dir == UP) $y -= 1;
      if ($dir == DOWN) $y += 1;
      if ($dir == LEFT) $x -= 1;
      if ($dir == RIGHT) $x += 1;
    } else {
      $dir = ($dir + 1) & 3;
    }
    $i --;
  }
}

if (isset($_POST['regex'])) {
  echo '<pre>' . $_POST['regex'] . '</pre>';
  run($_POST['regex']);
}

?>
<table>
  <?php foreach (MAZE as $y => $row) { ?>
    <tr>
      <?php foreach(str_split($row) as $x => $char) { ?>
        <td <?= $char != ' ' ? 'class=black' : '' ?> title="(<?= $x ?>, <?= $y ?>)"><?= $char ?></td>
      <?php } ?>
    </tr>
  <?php } ?>
</table>
<style>
  table {
    border-collapse: collapse;
  }
  td {
    width: 1em;
    height: 1em;
  }
  .black {
    background-color: black;
  }
</style>
<form method="POST">
  <p>
    <label>Regex <input type="text" name="regex"></label>
  </p>
  <p><button>Run</button></p>
</form>
