<!-- resources/views/components/mic-and-speak.blade.php -->
<style>
    .mini-modal {
        display: none;
        position: absolute;
        z-index: 1;
        padding: 10px;
        background-color: #fefefe;
        border: 1px solid #888;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 200px;
        /* Adjust width as needed */
    }

    .mini-modal-content {
        background-color: #fefefe;
        padding: 10px;
        border-radius: 4px;
    }

    .mini-modal-content h2 {
        font-size: 16px;
        margin-top: 0;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }
</style>
<style>
    .group-input {
        margin-bottom: 20px;
    }

    .mic-btn,
    .speak-btn {
        background: none;
        border: none;
        outline: none;
        cursor: pointer;
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        box-shadow: none;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .mic-btn i,
    .speak-btn i {
        color: black;
    }

    .mic-btn:focus,
    .mic-btn:hover,
    .mic-btn:active,
    .speak-btn:focus,
    .speak-btn:hover,
    .speak-btn:active {
        box-shadow: none;
    }

    .relative-container {
        position: relative;
    }

    .relative-container input {
        width: 100%;
        padding-right: 40px;
    }

    .relative-container:hover .mic-btn {
        opacity: 1;
    }

    .relative-container:hover .speak-btn {
        opacity: 1;
    }
</style>

<style>
    #start-record-btn {
        background: none;
        border: none;
        outline: none;
        cursor: pointer;
    }

    #start-record-btn i {
        color: black;
        /* Set the color of the icon */
        box-shadow: none;
        /* Remove shadow */
    }

    #start-record-btn:focus,
    #start-record-btn:hover,
    #start-record-btn:active {
        box-shadow: none;
        /* Remove shadow on hover/focus/active */
    }
</style>
<style>
    .mic-btn {
        background: none;
        border: none;
        outline: none;
        cursor: pointer;
        position: absolute;
        right: 10px;
        /* Position the button at the right corner */
        top: 50%;
        /* Center the button vertically */
        transform: translateY(-50%);
        /* Adjust for the button's height */
        box-shadow: none;
        /* Remove shadow */
    }

    .mic-btn {
        right: 50px;
        /* Adjust position to avoid overlap with speaker button */
    }

    .speak-btn {
        right: 16px;
    }

    .mic-btn i {
        color: black;
        /* Set the color of the icon */
        // box-shadow: none; /* Remove shadow */
    }

    .mic-btn:focus,
    .mic-btn:hover,
    .mic-btn:active {
        box-shadow: none;
        /* Remove shadow on hover/focus/active */
        // display: none;
    }

    .relative-container {
        position: relative;
    }

    .relative-container textarea {
        width: 100%;
        padding-right: 40px;
        /* Ensure the text does not overlap the button */
    }
</style>




<button class="mic-btn" type="button" style="display: none;">
    <i class="fas fa-microphone"></i>
</button>
<button class="speak-btn" type="button">
    <i class="fas fa-volume-up"></i>
</button>
<div class="mini-modal">
    <div class="mini-modal-content">
        <span class="close">&times;</span>
        <h2>Select Language</h2>
        <select id="language-select">
            <option value="en-us">English</option>
            <option value="hi-in">Hindi</option>
            <option value="te-in">Telugu</option>
            <option value="fr-fr">French</option>
            <option value="es-es">Spanish</option>
            <option value="zh-cn">Chinese (Mandarin)</option>
            <option value="ja-jp">Japanese</option>
            <option value="de-de">German</option>
            <option value="ru-ru">Russian</option>
            <option value="ko-kr">Korean</option>
            <option value="it-it">Italian</option>
            <option value="pt-br">Portuguese (Brazil)</option>
            <option value="ar-sa">Arabic</option>
            <option value="bn-in">Bengali</option>
            <option value="pa-in">Punjabi</option>
            <option value="mr-in">Marathi</option>
            <option value="gu-in">Gujarati</option>
            <option value="ur-pk">Urdu</option>
            <option value="ta-in">Tamil</option>
            <option value="kn-in">Kannada</option>
            <option value="ml-in">Malayalam</option>
            <option value="or-in">Odia</option>
            <option value="as-in">Assamese</option>
            <!-- Add more languages as needed -->
        </select>
        <button id="select-language-btn">Select</button>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recognition = new(window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';

        function startRecognition(targetElement) {
            recognition.start();
            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                targetElement.value += transcript;
            };
            recognition.onerror = function(event) {
                console.error(event.error);
            };
        }

        document.addEventListener('click', function(event) {
            if (event.target.closest('.mic-btn')) {
                const button = event.target.closest('.mic-btn');
                const inputField = button.previousElementSibling;
                if (inputField && inputField.classList.contains('mic-input')) {
                    startRecognition(inputField);
                }
            }
        });

        document.querySelectorAll('.mic-input').forEach(input => {
            input.addEventListener('focus', function() {
                const micBtn = this.nextElementSibling;
                if (micBtn && micBtn.classList.contains('mic-btn')) {
                    micBtn.style.display = 'inline-block';
                }
            });

            input.addEventListener('blur', function() {
                const micBtn = this.nextElementSibling;
                if (micBtn && micBtn.classList.contains('mic-btn')) {
                    setTimeout(() => {
                        micBtn.style.display = 'none';
                    }, 200); // Delay to prevent button from hiding immediately when clicked
                }
            });
        });
    });
</script>


<script>
    $(document).ready(function() {
        let audio = null;
        let selectedLanguage = 'en-us'; // Default language
        let inputText = '';

        // When the user clicks the button, open the mini modal 
        $(document).on('click', '.speak-btn', function() {
            let inputField = $(this).siblings('textarea, input');
            inputText = inputField.val();
            let modal = $(this).siblings('.mini-modal');
            if (inputText) {
                // Store the input field element
                $(modal).data('inputField', inputField);
                modal.css({
                    display: 'block',
                    top: $(this).position().top - modal.outerHeight() - 10,
                    left: $(this).position().left + $(this).outerWidth() - modal.outerWidth()
                });
            }
        });

        // When the user clicks on <span> (x), close the mini modal
        $(document).on('click', '.close', function() {
            $(this).closest('.mini-modal').css('display', 'none');
        });

        // When the user selects a language and clicks the button
        $(document).on('click', '#select-language-btn', function(event) {
            event.preventDefault(); // Prevent form submission
            let modal = $(this).closest('.mini-modal');
            selectedLanguage = modal.find('#language-select').val();
            let inputField = modal.data('inputField');
            let textToSpeak = inputText;

            if (textToSpeak) {
                if (audio) {
                    audio.pause();
                    audio.currentTime = 0;
                }

                // Translate the text before converting to speech
                translateText(textToSpeak, selectedLanguage).then(translatedText => {
                    const apiKey = '7fdc735bbfea4bfab96b30db2001d0cc';
                    const url =
                        `https://api.voicerss.org/?key=${apiKey}&hl=${selectedLanguage}&src=${encodeURIComponent(translatedText)}&r=0&c=WAV&f=44khz_16bit_stereo`;
                    audio = new Audio(url);
                    audio.play();
                    audio.onended = function() {
                        audio = null;
                    };
                });

            }

            modal.css('display', 'none');
        });

        // Speech-to-Text functionality
        const recognition = new(window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';

        function startRecognition(targetElement) {
            recognition.start();
            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                targetElement.value += transcript;
            };
            recognition.onerror = function(event) {
                console.error(event.error);
            };
        }

        $(document).on('click', '.mic-btn', function() {
            const inputField = $(this).siblings('textarea, input');
            startRecognition(inputField[0]);
        });

        // Show mic button on hover
        $('.relative-container').hover(
            function() {
                $(this).find('.mic-btn').show();
            },
            function() {
                $(this).find('.mic-btn').hide();
            }
        );

        // Function to translate text using RapidAPI
        async function translateText(text, targetLanguage) {
            const url = 'https://google-translate1.p.rapidapi.com/language/translate/v2';
            const options = {
                method: 'POST',
                headers: {
                    'x-rapidapi-key': 'd643df7db0msh30dc3dc2b5d04b8p12c47cjsnc87514f75cb8',
                    'x-rapidapi-host': 'google-translate1.p.rapidapi.com',
                    'Accept-Encoding': 'application/gzip',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    q: text,
                    target: targetLanguage.split('-')[0] // Get the language code only
                })
            };

            const response = await fetch(url, options);
            const data = await response.json();
            return data.data.translations[0].translatedText;
        }
    });
</script>

