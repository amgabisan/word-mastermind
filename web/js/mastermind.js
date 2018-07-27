$(document).ready(function() {
    var timeSpent = null;
    var turns = 10;
    var moves = 0;
    
    // Hide resultContainer
    $('#resultContainer').hide();
    $('#failedContainer').hide();
    $('#successContainer').hide();
    
    // Initialize Timer
    var clock = $('#timerContainer').FlipClock({
        onStop: function() {
		  timeSpent = clock.time.time;
	   },
    });
    
    // Get random 5-letter word in the dictionary
    $.post("/mastermind/manage/play")
        .done(function (data) {
            if (!data) {
                console.log('Error in the backend. Get random word');
            }      
        }
    );
    
    // User clicks the guess button
    $('#guessBtn').click(function() {
        var guessWord = $('#guessWord').val();

        if (guessWord.length == 5 && !/[^a-zA-Z]/.test(guessWord) ) {
            $('#errorMsg').text('');
            var timeSpent = clock.time.time;
            
            // Check if it matches
            $.post("/mastermind/manage/check", {word: guessWord, timeSpent: timeSpent})
                .done(function (data) {
                    data = JSON.parse(data);
                    $('#numberOfTurn').text(data.turns);
                    $('#numberOfMoves').text(data.moves);
                    
                    if (data.result == 'correct') {
                        $('#successContainer').show();
                        clock.stop();
                        $('#guessWord').prop('disabled', true); // Disable input field
                        $('.btn').prop('disabled', true); // Disable buttons
                    } 
                
                    if (data.turns == 0 && data.result != 'correct') {
                        $('#failedContainer').show();
                        $('#correctWord').text(data.word);
                        clock.stop();
                        $('#guessWord').prop('disabled', true); // Disable input field
                        $('.btn').prop('disabled', true); // Disable buttons
                    }
                
                    if (data.attempt.length != 0) {
                        $('#resultContainer tbody').empty();
                        $('#resultContainer').show();
                        var i = 1;
                        var checkHtml = '<i class="fas fa-check-double"></i>';
                        var wrongHtml = '<i class="fas fa-times"></i>';
                        var misplacedHtml = '<i class="fas fa-exchange-alt"></i>';
                        
                        $.each(data.attempt, function (index, value) {
                            var html = '<tr>' 
                                     + '<td class="text-center">' + i + '</td>'
                                     + '<td class="text-center">' + index + '</td>'
                                     + '<td class="text-center">';
                            
                            
                            if (value == true) {
                                html += '<ul class="list-inline">'
                                     +  '<li>' + checkHtml + '</li>'
                                     +  '<li>' + checkHtml + '</li>'
                                     +  '<li>' + checkHtml + '</li>'
                                     +  '<li>' + checkHtml + '</li>'
                                     +  '<li>' + checkHtml + '</li>'
                                     +  '</ul>';
                            } else if (value == false) {
                                 html += 'Invalid Input';
                            } else {
                                html += '<ul class="list-inline">';
                                for (var j = 0; j < value.length; j++) { 
                                    html += '<li>';
                                    
                                    if (value[j] == 0) {
                                        html += wrongHtml;
                                    } else if (value[j] == 1) {
                                        html += checkHtml;
                                    } else {
                                        html += misplacedHtml;
                                    }
                                    
                                    html += '</li>';
                                }
                                html += '</ul>';
                            }
                            
                            html += '</td></tr>';
                            i++;
                            $('#resultContainer tbody').prepend(html);
                        });
                    }
                }
            );
            $('#guessWord').val('');
        } else {
            $('#errorMsg').text('The guess word should only be 5 letters and contain alphabet letters');
        }
    
    });
    
    $('#quitBtn').click(function() {
        swal({
          title: 'Are you sure you want to give up?',
          text: "You won't be able to revert this!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, I Give up!',
          cancelButtonText: 'No, Cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result) {
              $.ajax({
                type: 'POST',
                data: {"giveUp" : "true"},
                url: '/mastermind/manage/check',
                success: function (data) {
                    $('#failedContainer').show();
                    $('#correctWord').text(data);
                    clock.stop();
                    $('#guessWord').prop('disabled', true); // Disable input field
                    $('.btn').prop('disabled', true); // Disable buttons
                },
            });
          }
        });
    });

});

