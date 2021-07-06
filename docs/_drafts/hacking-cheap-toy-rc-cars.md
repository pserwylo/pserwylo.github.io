---
layout: post
title: Hacking cheap toy RC cars
description: 
summary: 
tags: software-dev kotlin libgdx
minute: 1
---

2013-08-12

For a while now, I've been wanting to learn electronics. As a software developer, there is something about building software that interfaces with the real world which is really cool. I've spent a few months toying with Arduino's and infrared transmitters (for laser tag guns - but that is for another post), I'm getting to a stage where I'm comfortable cobbling simple circuits together.

After looking around for a project to work on, it turns out that it is surprisingly basic and fun to hack RC toy cars, to allow you to control them with an Arduino. So although I am only a beginner who is learning electronics for a bit of fun, I will be writing about it in the hope that other people will find this sort of stuff interesting as well.

This first post will explain the various approaches for connecting an Arduino (or other microcontroller) to a cheap toy RC car. In later posts, I'll document in detail the approach I've taken for my project.
<h2>How it's normally done...</h2>
There are two main approaches to hacking cheap to RC cars. Both of these rely on controlling the four main functions of a car:
<ul>
	<li>Forwards</li>
	<li>Backwards</li>
	<li>Left</li>
	<li>Right</li>
</ul>
(If you look at the datasheets for the transmitter/receiver chips (RX/TX chips) inside your car, they often have a fifth "turbo" control, but these seem to not be implemented by most cheap toys). More expensive cars have potentiometers which give variable control of left/right and forward/back, but the cheap ones tend to just be plain on/off. The two methods of controlling forwards/backwards and left/right are to:
<ol>
	<li>Hack the transmitter to control it with an Arduino</li>
	<li>Hack the car directly to control it with an Arduino</li>
</ol>
Both have advantages and disadvantages, and I'll elaborate on these below.
<h3>Hacking the car directly</h3>
This generally consists of:
<ul>
	<li>Opening up the car</li>
	<li>Connecting Arduino output pins to the <a href="https://en.wikipedia.org/wiki/H-bridge">H-bridge</a> of the car.</li>
	<li>Sending output from the Arduino to drive/steer the car.</li>
</ul>
Hacking the car directly allows you to have a truly autonomous vehicle that need not take instructions from anywhere. If you want to, you can connect Bluetooth or WiFi modules to the Arduino, so it can be controlled by a computer or a smart phone. Of course, if you hack the TX, this is possible too, but it is another unnecessary step (i.e. "Computer -&gt; Arduino -&gt; Transmitter -&gt; Car" vs "Computer -&gt; Arduino -&gt; Car"). The less steps involved, the less chance for software bugs or hardware problems to creep in.

One downside is that you need to physically attach the Arduino to the car. This means fixing it to the chassis somehow, and doing it well (if you plan on being able to smash into things without the Arduino detaching or breaking).  Here are some links regarding directly hacking the car with an Arduino (I'll update these lists as I come across others):
<ul>
	<li><a href="http://www.instructables.com/id/Autonomous-Control-of-RC-Car-Using-Arduino/?ALLSTEPS">Great writeup with images.</a></li>
	<li><a href="http://www.robotc.net/wiki/Tutorials/Arduino_Projects/RC_car_Hacking_Project/Connecting_the_Arduino">Just some pointers on attaching the Arduino and a breadboard (no wiring yet).</a></li>
	<li><a href="http://www.benrady.com/2013/01/arduino-controlled-rc-car.html">Adding an ultrasonic sensor to find obstacles.</a></li>
	<li><a href="http://www.instructables.com/id/RC-Car-to-Robot/?ALLSTEPS">Very detailed build, with an emphasis on cosntruction.</a></li>
</ul>
<h3>Hacking the transmitter</h3>
Hacking the transmitter means that you can leave the car untouched, and don't need to have an Arduino physically attached to the car and following it around. This makes hacking really small cars easier. Also, for bigger cars, you don't need to go to the effort of ensuring that the Arduino and all of the wires are ruggedly attached to your car.

As mentioned above, one downside is that there is one more place where things can go wrong. If you directly control the car via the Arduino, then there are less points of failure.

Here are some links about hacking the transmitter with an Arduino (I'll update these lists as I come across others):
<ul>
	<li><a href="http://www.instructables.com/id/Arduino-controls-cheap-RC-car-transmitter/?ALLSTEPS">Great writeup with detailed and annotated images.</a></li>
	<li><a href="http://jbprojects.net/articles/programmable-rc/">Ready made solution for hacking a transmitter.</a></li>
</ul>
<h2>...and then there's a third way...</h2>
I was interested in a somewhat different approach: Connect an Arudino directly to the car, intercept the signals (after the radio waves have been decoded by the RX receiver chip), and route them through the Arduino. That way, you have the option of passing them on to the motors to do their thing as requested, or prevent them from being forwarded on to the motors.

The upsides here are that you still get to drive the car manually, which is part of the fun of having an RC car, using the original transmitter, but it will have the option of being switched into autonomous mode if desired. I think this is the best of both worlds, giving the most flexibility (despite still having to find a place on the car to attach the Arduino).

This will be the topic of my next post, where I show how I did it with a $12 car I picked up from the shops.
