<h1><?php echo $seasonData['season']['name'] ?> </h1>

<h2>Teams</h2>
<?php foreach ($seasonData['season']['teams'] as $key => $team) :?>
  <div><?php echo $team['name'] ?></div>
<?php endforeach; ?>

</br>
<div class="clearfix"></div>



<h2>Matches</h2>
<?php foreach ($seasonData['season']['matches'] as $key => $match) :?>
  <div class="row">
    <div>
      <div class="xcol-sm-6 team-head team-a">
        <div class="">
          <div class="team">
            <div >
              <h2> <?php echo $match['teams']['a']['name'] ?> vs <?php echo $match['teams']['b']['name'] ?></h2>
            </div>
          </div>             
        </div>
      </div>
    
    </div>
    <div>
      <span>Venue : <?php echo $match['venue']?></span></br>
      <?php 
          $dateIso = $match['start_date']['iso'];
          $time = strtotime($date);
          $dateStr = date('l, F jS Y \a\t g:ia', $time);
      ?>
      <span class="date-content" date-content="<?php echo $dateIso ?>">Start Date : 
        <?php echo $dateStr. ' GMT'; ?>
      </span>
      </br>
      <span>Format : 
        <?php 
          if($match['format'] == 't20'){
            echo "T20";
          }else if($match['format'] == 'one-day'){
            echo "One Day";
          }else if($match['format'] == 'test'){
            echo "Test";
          }
        ?>
      </span></br>
    </div>
  </div>
  </br>
<?php endforeach; ?>

</br>
<div class="clearfix"></div>

<h3>Rounds</h3>
<?php foreach ($seasonData['season']['rounds_info'] as $roundKey) :?>
  <h4><?php echo $seasonData['season']['rounds'][$roundKey]['name']?></h4>
  <h5>Matches</h5>
  <?php foreach ($seasonData['season']['rounds'][$roundKey]['groups'] as $key => $group) :?>
    <?php foreach ($group['matches'] as $gMatchKey) :?>
      <?php echo $gMatchKey ?>
      </br>
    <?php endforeach; ?>
  <?php endforeach; ?>
<?php endforeach; ?>

<script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery('[date-content]').each(function(){
      var ele = jQuery(this);
      var dateIso = ele.attr('date-content');
      var dateTime = moment(dateIso);
      var localDateTime = dateTime.format('MMMM Do YYYY, h:mm:ss a');
      ele.text("Start Date: " + localDateTime + " (local time)");
    });
  });
</script>
