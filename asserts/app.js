var express = require('express');
var app = express();

app.get('/', function (req, res) {
  res.sendFile(__dirname + '/sample.html');
});

app.post('/api/', function (req, res) {
  if(req.query.action == 'lzmatch'){
    res.sendFile(__dirname + '/sampledata/completed.json');
  }else{
    res.sendJSON({error:true});
  }
});

app.use('/views', express.static('views'));
app.use('/js', express.static('js'));
app.use('/less', express.static('less'));
app.use('/css', express.static('css'));

var server = app.listen(3000, function () {
  var host = server.address().address;
  var port = server.address().port;
  console.log('Example app listening at http://%s:%s', host, port);
});