// I don't like to host code that I did not create. Though in this case it's required.
// I also don't understand how to use this tool yet. Eventually I want to code it in to mars2020.php myself.
// https://stackoverflow.com/a/28506533
var webPage = require('webpage');
var page = webPage.create();

page.open('https://jakesta13.github.io/Percy_Rover_Image_Grab/', function(status) {
 console.log(page.content);
  phantom.exit();
});