if (typeof(webkitSpeechRecognition) !== 'function') {              
            $('#status-bar').html('We are sorry but Dictation requires the latest version of Google Chrome on your desktop or Android Mobile.');
          } else {
            var recognizing;
            var recognition = new webkitSpeechRecognition();
            recognition.lang = 'en-us';
            recognition.continuous = false;
            recognition.interimResults = false;

            recognition.onresult = function(event) {

              var interim_transcript = '';
              var final_transcript = '';

              for (var i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                  final_transcript += event.results[i][0].transcript;
                  // console.log('Final transcript: ' + final_transcript);

                  var lala = final_transcript.split(" ");
                  var dic = ['charlie', 'Charlie', 'Charry', 'charry', 'sorry', 'Sorry', 'Charly', 'charly', 'garry', 'Garry'];
                  if ( jQuery.inArray(lala[0], dic) !== -1 && jQuery.inArray(lala[1], dic) !== -1 ) {
                    if (lala[2] != undefined) {
                      // Todo OK!
                      // console.log('Logica random');
                      var rand = randomG();                   

                      $('#pencil').css({transform: 'rotate(' + rand + 'deg)'});
                      setTimeout(function() {
                        var urlFB = 'http://www.facebook.com/sharer.php?s=100&p[title]='+encodeURIComponent('#CharlieCharlieChallenge') + '&p[summary]=' + encodeURIComponent('I\'m playing with Charlie and he is answering me! Don\'t be afraid, you can play too. http://charlie-charliechallenge.com #charliecharliechallenge') + '&p[url]=' + encodeURIComponent('http://charlie-charliechallenge.com') + '&p[images][0]=' + encodeURIComponent('http://charlie-charliechallenge.com/img/ogFacebook.jpg');
                        
                        if (rand > 389 && rand < 421 || rand > 569 && rand < 601 ) {
                          ga('send', 'event', 'Response', 'Charlie said', 'Yes', 1);
                          $('#response-title').html('#CharlieCharlieChallenge #SaysYes');
                          $('#response').html('Charlie says yes.');
                          $('#status-bar').html('<a href="' + urlFB + '" target="_blank" class="btn btn-social btn-facebook"><i class="fa fa-facebook"></i> Share on Facebook</a> <a href="https://twitter.com/intent/tweet?text=I%27m%20playing%20with%20Charlie%20and%20he%20is%20answering%20me%21%20Don%27t%20be%20afraid%2C%20you%20can%20play%20too.&url=http%3A%2F%2Fcharlie-charliechallenge.com&hashtags=CharlieCharlieChallenge,SaysYes" target="_blank" class="btn btn-social btn-twitter"><i class="fa fa-twitter"></i> Share on Twitter</a>');

                        } else {
                          ga('send', 'event', 'Response', 'Charlie said', 'No', 0);
                          $('#response-title').html('#CharlieCharlieChallenge #SaysNO');
                          $('#response').html('Charlie says no.');
                          $('#status-bar').html('<a href="' + urlFB + '" target="_blank" class="btn btn-social btn-facebook"><i class="fa fa-facebook"></i> Share on Facebook</a> <a href="https://twitter.com/intent/tweet?text=I%27m%20playing%20with%20Charlie%20and%20he%20is%20answering%20me%21%20Don%27t%20be%20afraid%2C%20you%20can%20play%20too.&url=http%3A%2F%2Fcharlie-charliechallenge.com&hashtags=CharlieCharlieChallenge,SaysNO" target="_blank" class="btn btn-social btn-twitter"><i class="fa fa-twitter"></i> Share on Twitter</a>');
                        }

                        $('#myModal').modal('show');
                      }, 7000);

                    } else {
                      // Dijiste Charlie Charlie pero nada más...
                      // Show Tell me something...
                      // console.log('Tell me something...');
                      showModal('Charlie is answering someone else right now. Try again.');
                    }
                  } else {
                    // No dijiste Charlie Charlie...
                    // console.log('Works saying "Charlie Charlie"');
                    showModal('Charlie is answering someone else right now. Try again.');
                  };

                  toggleStartStop();

                }

              }

            }

          }
      
          toggleStartStop();

          function toggleStartStop() {
            if (recognizing) {
              recognition.stop();
              recognizing = false;
            } else {
              recognition.start();
              recognizing = true;
            }
          }

          function showModal(phrase) {
            $('#status-bar').html(phrase);
            $('#myModal').modal('show');
          };

          $('#myModal').on('hidden.bs.modal', function (e) {
            location.reload();
          })

          function randomG() {

            // 390-420 YES
            // 480-510 NO
            // 570-600 YES
            // 655-685 NO
            var myArray = [390, 395, 400, 405, 410, 415, 420, 480, 485, 490, 495, 500, 505, 510, 570, 575, 580, 585, 590, 595, 600, 655, 660, 665, 670, 675, 680, 685];
            var rand = myArray[Math.floor(Math.random() * myArray.length)];

            

            /*
            var ooo = Math.floor(Math.random() * 720) + 375;
            if ( ooo > 425 && ooo < 475 || ooo > 525 && ooo < 560 || ooo > 600 && ooo < 670 || ooo > 700 && ooo < 740 || ooo > 790 && ooo < 835 || ooo > 885 && ooo < 920 || ooo > 965 && ooo < 1015 || ooo > 1060 && ooo < 1100 ) {
              randomG();
            };
            */

            return rand;
          };


          $('#again').on('click', function (e) {
            location.reload();
          })