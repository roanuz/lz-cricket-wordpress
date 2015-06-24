if(typeof(LZCONFIG) === 'undefined'){
  LZCONFIG = {
    'templateUrl': '../views/',
    'ajaxUrl': '/api/',
  };  
}


angular.module('lzCricket', [
  'ngAnimate'
])
.constant('appConfig', {
  path: {
    img: 'http://img.litzscore.com/',
  },
  cricket: {
    possibleInnings: ['1', '2', '9'],
    possibleTeams: ['a', 'b'],    
  }
})

.service('lzAPI', function($q, $http){
  
  var LZAPI = function(){

  };

  LZAPI.prototype.getMatch = function(key, sec){
    var def = $q.defer();
    $http({
      url: LZCONFIG.ajaxUrl,// + '/?action=lzmatch',
      method: "POST",
      params: {
        'action': 'lzmatch',
        'key': key,
        'sec': sec
      }
    })
    .success(function(data, status, headers, config){
      if(data && data.data && data.data.card){
        def.resolve(data.data.card);
      }else{
        def.reject(data, status);
      }
    })
    .error(function(data, status, headers, config){
      def.reject(data, status);
    });

    return def.promise;
  };

  return new LZAPI();
})
.directive('lzCricketMatch', function(lzAPI){
  return {
    restrict: 'EA',
    replace: true,
    templateUrl: LZCONFIG.templateUrl + 'lz-cricket-match.html',
    scope: {
      'lzCricketMatch': '@',
      'sec': '@'
    },

    controller: function($scope, $element, $attrs, appConfig, $timeout) {
      $scope.dataStatus = 'loading'; //'ready', 'error'
      $scope.match = null;
      $scope.activeView = null;
      $scope.appConfig = appConfig;

      $scope.teamFlagUrl = function(key){
        if(LZCONFIG['flags'] && LZCONFIG.flags[key]){
          return LZCONFIG.flags[key];
        }else{
          return 'http://img.litzscore.com/flags/' + key + '_s.png'
        }
      }


      function onMatchUpdate(match){

        $scope.match = match;
        

        var mDate = moment.unix(match.start_date.timestamp);
        var deltaDays = Math.abs(mDate.diff(moment(), 'days'));
        var dateStr = mDate.format("dddd, MMMM Do YYYY, h:mm:ss a");
        
        if( deltaDays < 7 ){
          dateStr = mDate.calendar();
        }

        match.start_date.show_cal = dateStr;
        match.start_date.starts_in = mDate.fromNow();

        if(Math.abs(mDate.diff(moment(), 'minutes')) < 2){
          match.start_date.starts_in = 'Now';        
        }

        if(match.winner_team){
          match.loser_team = (match.winner_team == 'a')?'b':'a';
        }


        match.allInnings = {};
        for(var i=0, b=null; b=match.batting_order[i]; i++){
          var k = b[0] + '_' + b[1];
          match.allInnings[k] = match.innings[k];
          match.allInnings[k].teamKey = b[0];
          match.allInnings[k].inningsNumber = b[1];
        }

        if(!($scope.activeView) && match.status != 'notstarted'){
          $scope.activeView = 'scorecard';
        }

        $scope.dataStatus = 'ready';          
        D = match;
        console.log(D);

        $timeout(function(){
          lzAPI.getMatch($scope.lzCricketMatch, 
            $scope.sec).then(onMatchUpdate);
        }, 1000 * 30);


      }

      lzAPI.getMatch($scope.lzCricketMatch, $scope.sec).then(
        onMatchUpdate,
        function(er){
          $scope.dataStatus = 'error';
        }
      );

    }

  }
});