## NOTES

Upload this code to your Arduino for 1 led:

void setup()
{
  Serial.begin(115200);
}
void loop()
{
  while(!Serial.available());	//wait until a byte was received
  analogWrite(3, Serial.read());//output received byte
}




 This example code is for 5 buttons i hard wired onto breadboard to control the water pistol. I have it working when i push the real button i wired up but am lost for now as to how to get this interface into a webpage for online button pressing making the robot move...
 
#include <Servo.h>

Servo Tservo;  // create servo object to control a servo
Servo Bservo;  // create servo object to control a servo

int TservoPin=9;   //Servo is hooked to pin 9
int BservoPin=6;   //Servo is hooked to pin 9

int buttonUP = 2;    // digital pin 2 has a RED pushbutton attached to it.
int buttonDOWN = 4;  // digital pin 4 has a RED pushbutton attached to it.
int buttonLEFT = 12;  // digital pin 7 has a BLUE pushbutton attached to it.
int buttonRIGHT = 7; // digital pin 8 has a BLUE pushbutton attached to it.
int buttonFIRE = 8; // digital pin 8 has a BLUE pushbutton attached to it.
int ledPin = 13;       // the pin that the LED is attached to
int Tpos = 0;    // variable to store the TOP servo position
int Bpos = 0;    // variable to store the BOTTOM servo position
int LRservoDelay=50; // 25 millisecond delay after each servo write
int UDservoDelay=80; // 25 millisecond delay after each servo write
int fireDelay=2000; // 25 millisecond delay after each servo write

int buttonUPstate =0; //declaring variable
int buttonDOWNstate =0; //declaring variable
int buttonLEFTstate =0; //declaring variable
int buttonRIGHTstate =0; //declaring variable
int buttonFIREstate =0; //declaring variable

int shotCounter = 0;
int shotButtonState =0; //declaring variable

// the setup routine runs once when you press reset:
void setup() {
  Serial.begin(9600);   // initialize serial communication at 9600 bits per second:
  pinMode(buttonUP, INPUT);    // make the pushbutton's pin an input:
  pinMode(buttonDOWN, INPUT);   // make the pushbutton's pin an input:
  pinMode(buttonLEFT, INPUT);   // make the pushbutton's pin an input:
  pinMode(buttonRIGHT, INPUT);   // initialize the pushbutton's pin an input:
  pinMode(buttonFIRE, INPUT);   // initialize the pushbutton's pin an input:
  pinMode(ledPin, OUTPUT);  // make the ledPin an OUTPUT:
  Tservo.attach(TservoPin);  // attaches the servo myPointer to pin servoPin
  Bservo.attach(BservoPin);  // attaches the servo myPointer to pin servoPin
  Tpos = 95;
  Bpos = 95;
}

// the loop routine runs over and over again forever:

void loop() {
  buttonLEFTstate = digitalRead(buttonLEFT);  
  buttonRIGHTstate = digitalRead(buttonRIGHT);
  buttonUPstate = digitalRead(buttonUP);  
  buttonDOWNstate = digitalRead(buttonDOWN);  
  buttonFIREstate = digitalRead(buttonFIRE);  

if (buttonLEFTstate == HIGH && Tpos < 140 && buttonRIGHTstate == LOW ) {   
    Tservo.write(Tpos++);
    delay(LRservoDelay);}

if (buttonRIGHTstate == HIGH && Tpos > 50 && buttonLEFTstate == LOW ) {   
    Tservo.write(Tpos--);
    delay(LRservoDelay);}

if (buttonUPstate == HIGH && Bpos > 70 && buttonDOWNstate == LOW ) {   
    Bservo.write(Bpos--);
    delay(UDservoDelay);}

if (buttonDOWNstate == HIGH && Bpos < 130 && buttonUPstate == LOW ) {   
    Bservo.write(Bpos++);
    delay(UDservoDelay);}

 if (buttonFIREstate != shotButtonState) {
if (buttonFIREstate == HIGH ) {
    digitalWrite(ledPin, HIGH);
    shotCounter++;
    Serial.print(shotCounter);
    Serial.println(" Shots Fired");
    delay(fireDelay);
    digitalWrite(ledPin, LOW);}
shotButtonState = buttonFIREstate;
}

}
