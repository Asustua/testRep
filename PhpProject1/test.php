<head>
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
</head>
<body>
<?php

$iHeight = 9;
$iWidth = 9;

echo '<form action="" method="POST">';
echo '<div class="box">';
for($i = 0 ; $i < $iHeight; $i++) {
    echo '<div class="clear">';
    for($j = 0 ; $j < $iWidth ; $j++) {
        echo '<div class="nothing" data-positionx="'.$i.'" data-positiony="'.$j.'">';
        echo '<input type="hidden" name="spell['.$i.']['.$j.']" value="0" >';
        echo '</div>';
    }
    echo '</div>';
}
echo '</div>';
echo '<div style="clear: both;"></div><br>';
echo '<div class="controlls nothing" data-class="nothing" data-value="1"></div>';
echo '<div class="controlls spell" data-class="spell" data-value="2"></div>';
echo '<div class="controlls play" data-class="play" data-value=3"></div>';
echo '<input type="submit">';
echo '</form>';
?>
    
<script>
    var controll = 0;
    var controll_class = 'nothing';
    
    $('.controlls').click(function(){
        controll_class = $(this).data('class');
        controll = $(this).data('value');
    });
    
    $('.box > div > div').click(function(){
       $(this).removeClass();
       $(this).addClass(controll_class);
       $(this).children('input').val(controll);
    });
    
</script>

<style>
    
    .controlls {
        height: 32px;
        width: 32px;
        float: left;
        margin-right: 15px;
    }
    
    .nothing {
        border: 1px solid green;
    }
    
    .spell {
        border: 1px solid red;
        background: red;
    }
    
    .play {
        border: 1px solid blue;
        background: blue;
    }

    .box > div > div {
        height: 32px;
        width: 32px;
        float: left;
    }
    
    .clear {
        clear: both;
    }
    
</style>
</body>