<?php
require_once("config/config.php");

$elements = new elements();
$pageLayout = new pageLayout();

$params = array(
    'title' => 'Privacy Policy',
    'icon'  => 'fas fa-file-alt',
    'color' => '#5f6368',
);

function setHTMLContent()
{
    return '
        <h1>Property.CoZa DocuSign Privacy Policy</h1>
        <div style="margin-left: 20px; margin-right: 20px;">
            <p>Last Updated: <strong>August 10, 2021</strong></p>
            <p style="color: #666;">
                <strong>
                    IMPORTANT NOTICE: Every man who has lotted here 
                    over the centuries, has looked up to the light and 
                    imagined climbing to freedom. So easy, so simple! 
                    And like shipwrecked men turning to seawater foregoing 
                    uncontrollable thirst, many have died trying. 
                    And then here there can be no true despair without hope. 
                    So as I terrorize Gotham, I will feed its people hope to poison their souls. 
                    I will let them believe that they can survive so that you can watch them climbing over each other to stay in the sun. 
                    You can watch me torture an entire city. And then when you’ve truly understood the depth of your failure, we will fulfill Ra’s Al Ghul’s destiny. 
                    We will destroy Gotham. And then, when that is done, and Gotham is… ashes Then you have my permission to die.
                </strong>
            </p>
            <p>
                But we’ve met before. That was a long time ago, I was a kid at St. Swithin’s, It used to be funded by the Wayne Foundation. 
                It’s an orphanage. My mum died when I was small, it was a car accident. I don’t remember it. My dad got shot a couple of years later for a gambling debt. 
                Oh I remember that one just fine. Not a lot of people know what it feels like to be angry in your bones. I mean they understand. The fosters parents. 
                Everybody understands, for a while. Then they want the angry little kid to do something he knows he can’t do, move on. After a while they stop understanding. 
                They send the angry kid to a boy’s home, I figured it out too late. Yeah I learned to hide the anger, and practice smiling in the mirror. 
                It’s like putting on a mask. So you showed up this one day, in a cool car, pretty girl on your arm. We were so excited! Bruce Wayne, a billionaire orphan? 
                We used to make up stories about you man, legends and you know with the other kids, that’s all it was, just stories, but right when I saw you, I knew who you really were. 
                I’d seen that look on your face before. It’s the same one I taught myself. I don’t why you took the fault for Dent’s murder but I’m still a believer in the Batman. 
                Even if you’re not…
            </p>
            <p><strong>1. Lorem Ipsum</strong></p>
            <p>
                But we’ve met before. That was a long time ago, I was a kid at St. Swithin’s, It used to be funded by the Wayne Foundation. 
                It’s an orphanage. My mum died when I was small, it was a car accident. I don’t remember it. My dad got shot a couple of years later for a gambling debt. 
                Oh I remember that one just fine. Not a lot of people know what it feels like to be angry in your bones. I mean they understand. The fosters parents. 
                Everybody understands, for a while. Then they want the angry little kid to do something he knows he can’t do, move on. After a while they stop understanding. 
            </p>
            <p><strong>1.1 Lorem Ipsum</strong></p>
            <p>
                But we’ve met before. That was a long time ago, I was a kid at St. Swithin’s, It used to be funded by the Wayne Foundation. 
                It’s an orphanage. My mum died when I was small, it was a car accident. I don’t remember it. My dad got shot a couple of years later for a gambling debt. 
                Oh I remember that one just fine. Not a lot of people know what it feels like to be angry in your bones. I mean they understand. The fosters parents. 
                Everybody understands, for a while. Then they want the angry little kid to do something he knows he can’t do, move on. After a while they stop understanding. 
            </p>
            <p><strong>1.2 Lorem Ipsum</strong></p>
            <p>
                But we’ve met before. That was a long time ago, I was a kid at St. Swithin’s, It used to be funded by the Wayne Foundation. 
                It’s an orphanage. My mum died when I was small, it was a car accident. I don’t remember it. My dad got shot a couple of years later for a gambling debt. 
            </p>
            <p><strong>2. Lorem Ipsum</strong></p>
            <p>
                But we’ve met before. That was a long time ago, I was a kid at St. Swithin’s, It used to be funded by the Wayne Foundation. 
                It’s an orphanage. My mum died when I was small, it was a car accident. I don’t remember it. My dad got shot a couple of years later for a gambling debt. 
                Oh I remember that one just fine. Not a lot of people know what it feels like to be angry in your bones. I mean they understand. The fosters parents. 
                Everybody understands, for a while. Then they want the angry little kid to do something he knows he can’t do, move on. After a while they stop understanding. 
            </p>
        </div>
    ';
}

$html  = $pageLayout->getHeader();
$html .= $elements->navBar($params);
$html .= $elements->accountPanel();
$html .= '<div style="overflow: hidden; overflow-y: scroll" class="page-wrapper" id="content">';
$html .= setHTMLContent();
$html .= '</div>';
$html .= $pageLayout->getFooter();

echo $html;