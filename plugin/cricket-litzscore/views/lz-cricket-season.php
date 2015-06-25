<div class="lz lz-season <?php echo $attrs['theme'] ?>">
  <div class="row">
    <div class="col-md-12 lz-season-filter">
      <select data-lz-filter="team">
        <option value="all">All Teams</option>
        <?php foreach ($seasonData['season']['teams'] as $key => $team) :?>
          <option value="<?php echo strtolower($team['card_name']) ?>"><?php echo $team['name'] ?></option>
        <?php endforeach; ?>
      </select>      
    </div>
  </div>

  <div class="row">
    <?php foreach ($seasonData['season']['matches'] as $key => $match) :?>
    <div class="col-md-6 match-col" data-lz-filter-team-a="<?php echo $match['teams']['a']['key'] ?>" data-lz-filter-team-b="<?php echo $match['teams']['b']['key'] ?>">
      <div class="a-match">
        <a href="<?php echo $matchUrlPrefix. $match['key'] . '/' ?>">
          <div class="match-head lz-color-3-bg">
            <?php 
              $dateIso = $match['start_date']['iso'];
              $time = strtotime($match['start_date']['iso']);
              $dateStr = date('F jS Y \a\t g:ia', $time);
            ?>
            <div data-convert-to-local-time="<?php echo $dateIso ?>"><?php echo $dateStr. ' GMT'; ?>
            </div>
          </div>
          <div class="match-info">
            <div class="match-info-flags">
            <img src="<?php echo lzGetTeamLogoUrl($match['teams']['a']['key']); ?>" class="team-flag-a">
            <img src="<?php echo lzGetTeamLogoUrl($match['teams']['b']['key']); ?>" class="team-flag-b">
            </div>


            <div class="match-info-text">
              <div class="team-name-a"><?php echo $match['teams']['a']['name'] ?></div>
              <div class="team-vs-txt">VS</div>
              <div class="team-name-b"><?php echo $match['teams']['b']['name'] ?></div>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="match-foot lz-color-1-bg-r">
            <div>
              <?php echo $match['venue']?>
            </div>
          </div>
        </a>
      </div>
    </div>
    <?php endforeach; ?>   
  </div>   
</div>

<script type="text/javascript">
  if(jQuery){
    jQuery(document).ready(function(){
      jQuery('[data-convert-to-local-time]').each(function(){
        var ele = jQuery(this);
        var dateIso = ele.attr('data-convert-to-local-time');
        var dateTime = moment(dateIso);
        var localDateTime = dateTime.format('MMMM DD, YYYY h:mm a');
        ele.text(localDateTime);
      });

      jQuery('[data-lz-filter=team]').change(function(){
        var k = jQuery('[data-lz-filter=team]').val();
        jQuery('[data-lz-filter-team-a]').show();

        if(k != 'all'){  
          jQuery('[data-lz-filter-team-a]').hide();        
          jQuery('[data-lz-filter-team-a='+k+']').show();
          jQuery('[data-lz-filter-team-b='+k+']').show();
        }
      });

    });
  }
</script>
