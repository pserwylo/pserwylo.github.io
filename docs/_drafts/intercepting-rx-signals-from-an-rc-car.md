---
layout: post
title: Intercepting RX signals from an RC car
description: 
summary: 
tags: software-dev kotlin libgdx
minute: 1
---

2013-08-08

The general flow from pressing a button on a transmitter, to the RC car driving is as follows:
<ol>
	<li>Transmitter button pressed</li>
	<li>TX chip on transmitter sends radio waves through the antenna</li>
	<li>RX chip on the receiver/car decodes radio waves</li>
	<li>RX chip output pin sends digital signal to motors  (that I'll focus on in this post)</li>
</ol>
Lets walk through the steps required to connect an Arduino (or any microcontroller) to the RC car, and read the left/right forward/backward signals.
<h3>Find RX chip</h3>
After taking the casing off my toy car, there is only one PCB in the entire car. It has Two cables leading to the batteries, two to the steering motor and two to the driving motor (they are soldered onto the other side of the PCB in the image below). There is only one component on this PCB which looks like an RX chip, which is highlighted. RX chips tend to have 16 or so pins coming of them, so are often easily identifiable.

[caption id="attachment_160" align="alignnone" width="260"]<a href="http://peter.serwylo.com/wp-content/uploads/2013/08/pcb-rx-chip.png"><img class="size-medium wp-image-160" title="pcb-rx-chip" src="http://peter.serwylo.com/wp-content/uploads/2013/08/pcb-rx-chip-260x300.png" alt="" width="260" height="300" /></a> The PCB that was in my toy car. The RX chip is highlighted.[/caption]
<h3>Identify output pins</h3>
I've found the easiest way to figure out which pins on the RX chip correspond to which function (e.g. forward/backward or left/right) is to find a datasheet which explains it all. Looking carefully at my RX chip, I could see the identifier "RX-2B". <a href="https://duckduckgo.com/?q=rx-2b+datasheet">Searching the internet</a> for that gives numerous sources to <a href="http://www.datasheetdir.com/RX-2B+download">download the datasheet</a>. Most TX/RX chips in cheap Chinese toys have their datasheets available online. These are generally a really good source of info, even if, like me, you don't know much about electronics.

[caption id="attachment_162" align="alignnone" width="266"]<a href="http://peter.serwylo.com/wp-content/uploads/2013/08/RX-2B-datasheet.png"><img class="size-medium wp-image-162" title="RX-2B-datasheet" src="http://peter.serwylo.com/wp-content/uploads/2013/08/RX-2B-datasheet-266x300.png" alt="" width="266" height="300" /></a> Page from the datasheet that explains the pin configurations on the RX chip. The Right output pin is highlighted.[/caption]

Above is the relevant page from the datasheet, highlighting the pin for the "Right" function. Below is where it corresponds to on the actual pin:

[caption id="attachment_161" align="alignnone" width="260"]<a href="http://peter.serwylo.com/wp-content/uploads/2013/08/pcb-right-output-pin.png"><img class="size-medium wp-image-161" title="pcb-right-output-pin" src="http://peter.serwylo.com/wp-content/uploads/2013/08/pcb-right-output-pin-260x300.png" alt="" width="260" height="300" /></a> The right output pin on the car's PCB.[/caption]
<h3>Decide where to intercept/attach Arduino</h3>
The image below shows the copper trace from the "Right" output pin on the RX chip, through a surface mounted resistor, to the relevant transistor in the <a href="https://en.wikipedia.org/wiki/H_bridge">H-Bridge</a> (configuration of four transistors which allow the motor to run in one direction or the other).

[caption id="attachment_169" align="alignnone" width="260"]<a href="http://peter.serwylo.com/wp-content/uploads/2013/08/pcb-right-output-trace1.png"><img class="size-medium wp-image-169" title="pcb-right-output-trace" src="http://peter.serwylo.com/wp-content/uploads/2013/08/pcb-right-output-trace1-260x300.png" alt="PCB copper trace for the &quot;Right&quot; output pin." width="260" height="300" /></a> PCB copper trace for the "Right" output pin.[/caption]

My goal was to solder a wire onto this trace somewhere along the red line, so that I could then connect it to an input pin on the Arduino. I learnt how to do this from the tutorial <a href="http://www.instructables.com/id/Solder-on-PCB-traces/">"Solder on PCB traces"</a>. The general principle is: expose the copper you need to solder onto (because in my picture, it is coated in a green protective material) and, surprisingly enough, solder the wire to this bit of copper :)

I used a small flat head screwdriver to scrape away the coating along the long horizontal part of the trace to expose the copper. I found that soldering onto the track was particularly
<h3>Connect Ground from Arduino to PCB</h3>
I still learning electronics, but I have been told by the wonderful internet that when you have two electronic devices that are powered individually by different sources, you should connect the ground on both devices together.  It is to do with the fact that ground is really a reference point to measure other voltages against. I presume that when you use one device to measure voltages from another device, you are measuring the potential difference between the output voltage and the ground voltage from that device.  If your measuring device doesn't have the same gorund reference point, then the measurements will be all off, or something like that (I'm probably all wrong, but I'll learn over time what it all means).
<h3>Connect Wire from PCB Trace to Arduino Pin</h3>
I mentioned above that we should solder a wire onto the PCB trace leaving the "Right" pin on the RX chip. Now we should take this wire, and connect it to the Arduino. I'm using pin #5 (see source code below), but you can use any digital pin.

[caption id="attachment_176" align="alignnone" width="258"]<a href="http://peter.serwylo.com/wp-content/uploads/2013/08/pcb-connected-to-arduino.jpg"><img class="size-medium wp-image-176" title="pcb-connected-to-arduino" src="http://peter.serwylo.com/wp-content/uploads/2013/08/pcb-connected-to-arduino-258x300.jpg" alt="Little hard to see, but the blue wire has been soldered onto the PCB trace, and is connected to Arduino pin #5. Also note the ground from the Arduino is soldered onto the ground for the PCB." width="258" height="300" /></a> Little hard to see, but the blue wire has been soldered onto the PCB trace, and is connected to Arduino pin #5. Also note the ground from the Arduino is soldered onto the ground for the PCB.[/caption]
<h3>Arduino Source Code</h3>
Finally, here is the source code I've used to read the status from the "Right" pin on my RX-2B chip, after connecting it to the Arduino on pin 5. In a later post, I'll update this so that it will work for all functions, not just "right". More importantly, we'll optionally block the request to go right from heading to the steering servo. More on this later though, as this code just reads the input that it received from the TX.

[git:pre_c@https://github.com/pserwylo/rc-mario-cart/blob/b9cca074a9603ab94e097cc23c625439e03e11b3/rc_intercept.ino]
