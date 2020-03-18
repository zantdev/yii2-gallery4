<?php 
use kartik\file\FileInput;

?>

<label class="control-label"><?= $label ?></label>
<?= FileInput::widget($config);
?>