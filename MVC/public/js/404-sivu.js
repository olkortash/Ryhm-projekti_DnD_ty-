/*
 * Flirty - 404 Page
 * Build Date: October 2016
 * Author: joashp
 */

var transcript = new Array(
    "Umm... well i was wondering when you\'d show up...",
    "This is a page designed specifically if the devs did something wrong..",
    "Or you touched the header and tried to show off to your friends.",
    "Or the page was simply eradicated...",
    "In any case, this is not a page meant to be seen.",
    "It\'s an Error 404 page not found.",
    "You are pretty unlucky.",
    "So- Umm. I kinda have to tell you some legal stuff..",
    "BORING legal stuff.",
    "I have a transcript here somewhere...",
    "hang tight for a second, will you?",
    "Thanks man.",
    "Now where did i put it..?",
    "Here? No, not here.",
    "Yo boss! Where is the transcript-404? Huh?",
    "What do you mean that\'s not what it\'s called?",
    "\"The_sensual_customer_time_waste_script™\"?",
    "Alright! Alright, boss, where is it?",
    "I\'m not saying that! No way!",
    "Fine.. Where is \"The_sensual_customer_time_waste_script™\"?",
    "RIGHT THERE!? Folk... \'ight thanks boss... i guess.",
    "Here we are.",
    "Now we can start the script.",
    "\"The Masters© is the company you are in contact with.",
    "This is their website, in which something broke. Hence my presence",
    "We would appreciate if you wouldn\'t tell anybody about this.",
    "I am legally obligated to inform you,",
    "that we are not liable for the time wasted reading this script.",
    "And therefore you cannot sue us.",
    "Now in the case you do decide to sue. We have a protocol for it",
    "The protocoll is\"... Umm... Yo Jeff, what does this say?",
    "Who wrote this? This is down right unprofessional!",
    "Who wrote \"Tolk\" on The_sensual_customer_time_waste_script™?",
    "What does \"Tolk\" even mean?",
    "What am i supposed to do here? My script is just \"tolkalore\" from line 22 onwards.",
    "WHY DOES THIS SCRIPT GO PAST IMPORTANT PARTS!?",
    "The script™ just says \"contact us at, blah blah blah\"!",
    "Well, i guess it\'s break time for me.",
    "I need to contact HR about this.",
    "Yo customer.. or web-surfer... or dev(?)",
    "You can go now... if there is no button for it,",
    "Just press the arrow top-left corner that points left.",
    "You should be able to get back like that.",
    "If not, you are fresh out of luck... Dunno what to tell you man",
    "But i for one am doing something worth-while with my time.",
    "See, i\'m paid to do this, you coulda just left.",
    "Well, hope it was worth it i guess.",
    "Now if you\'ll excuse me, i will submit my 2 weeks notice.",
    "Byeeee",
    "Folk",
    "Tolk",
    "Tolkalore",
    "Folkalore",
    "Tolgalore",
    "...",
    "...",
    "...",
    "...",
    "...",
    "...",
    "..."
);

var speed = 40;
var index = 0;
text_pos = 0;
var str_length = transcript[0].length;
var contents, row;

function start_type_text() {
    contents = '';
    row = Math.max(0, index - 6);
    while (row < index)
        contents += transcript[row++] + '\r\n';
    document.textform.elements[0].value = contents + transcript[index].substring(0, text_pos) + "_";
    if (text_pos++ == str_length) {
        text_pos = 0;
        index++;
        if (index != transcript.length) {
            str_length = transcript[index].length;
            setTimeout("start_type_text()", 1500);
        }
    } else
        setTimeout("start_type_text()", speed);
}

function MM_callJS(jsStr) {
    return eval(jsStr)
}