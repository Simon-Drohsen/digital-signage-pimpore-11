const apiUrl = location.href+'/api';

const getData = async () => {
    return axios.get(apiUrl);
}

const data =  await getData();
let price = data.data;

window.setup = setup;
window.draw = draw;

const cnv = document.getElementById('canvas');
const ctx = cnv.getContext('2d');
let rotationStarted = false;
let giveName = false;
let byColor = false;
let wedgeImage = false;
let message = false;
ctx.willReadFrequently = true;
let angle = 360;
let rotationSpeed = 0.0;
let width = windowWidth;
let height = windowHeight;
let isSpinning = 0;
let wheelRotation = 0;
let counter = 0;
let tickCounter = 0
let wedgeSubdiv = angle / price.length;
let person = 'Obi-Wan Kenobi';
let angles = [];
let now = Date.now();
let delta;
let lastTime;
let pixelColor;
let test;
let tickPitch;
let snd_tick;

setup();

function preload() {
    snd_tick = loadSound('/fortune_wheel/tick.mp3')
}

function setup() {
    lastTime = Date.now();
    person = nameAlert();
    createCanvas(width, height, P2D, cnv);
    frameRate(144);
    noStroke();
    noCursor();
    loop();
}

function draw() {
    let now = Date.now();
    let wheelSpinSound = document.getElementById('wheel-sound');
    angleMode(DEGREES);
    translate(width / 2, height / 2);

    ellipse(0,0,865, 865);
    fill(0);

    delta = (now - lastTime) / 360;
    if (delta > 0) {
        wheelRotation += delta * rotationSpeed;
    }
    lastTime = Date.now();
    if (isSpinning === 1) {
        rotationSpeed = 15.4//random(5, 20);
        isSpinning = 2;
        rotationStarted = true;
    } else if (rotationStarted) {
        angle += rotationSpeed;
        let angleBefore = (angle - rotationSpeed) % wedgeSubdiv;
        let labelA = round(angleBefore / angle)
        let labelB = round(360 / angle)

        tickCounter += labelB - labelA
        rotate(angle);

        if (rotationSpeed > 0.025) {
            rotationSpeed *= 0.99;
        } else {
            rotationSpeed = 0;
        }
    }

    registerInputListeners();
    makeWedges();
    resetMatrix();
    drawTriangle();
}

function makeWedges() {
    let diameter = 850;
    let lastAngle = 0;
    rotate(270);

    for (let i = 0; i < price.length; i++) {
        angles.push(wedgeSubdiv);
        fill(price[i][2]);

        let currentAngle = lastAngle + angles[i];
        let labelRadius = diameter / 2;
        let labelX = cos((lastAngle + currentAngle) / 2) * labelRadius;
        let labelY = sin((lastAngle + currentAngle) / 2) * labelRadius;
        let textAngle = atan2(labelY, labelX);

        arc(0, 0, diameter, diameter, lastAngle, currentAngle);
        push();
        translate(labelX, labelY);
        rotate(textAngle);
        textAlign(RIGHT, CENTER);
        if (price[i][5]) {
            if (price[i][2] === '#160D74' || price[i][2] === '#175AFF') {
                fill('#E9EBE8');
            } else {
                fill('#000000');
            }
            textSize(16);
            if (wedgeImage) {
                text(price[i][0], -135, 0);
                fill('#E9EBE8');
                ellipse(-65,0,labelRadius / 4,labelRadius / 4);
            } else {
                text(price[i][0], -30, 0);
            }
        }

        pop();

        lastAngle = currentAngle;

        if (rotationSpeed === 0) {
            winMessage();
        }
    }
}

function registerInputListeners() {
    document.addEventListener('keyup', function (e) {
        if (e.key === 'Enter') {
            spinWheel();
        }
    });
}

function spinWheel() {
    isSpinning = 1;
    message = true;
}

function winMessage() {
    if (isSpinning === 2 && message) {
        let selectedPrice;
        let winningColor;
        let winModal = document.getElementById("myModal");
        let winningAudio = document.getElementById('winning-sound');
        message = false;

        winModal.style.display = "block";

        if (byColor) {
            switch (pixelColor[0]) {
                case 22:
                    winningColor = '#160D74';
                    break;
                case 23:
                    winningColor = '#175AFF';
                    break;
                case 227:
                    winningColor = '#E3F509';
                    break;
                case 255:
                    winningColor = '#FF6C64';
                    break;
            }

            for( let i = 0, len = price.length; i < len; i++ ) {
                if (price[i][2] === winningColor) {
                    selectedPrice = price[i];
                    console.log(selectedPrice);
                    break;
                }
            }

            winningMessage(selectedPrice);
        } else {
            selectedPrice = Math.floor((360 - (angle % 360)) / wedgeSubdiv);
            winningMessage(price[selectedPrice]);
        }

        winningAudio.play();

        setTimeout(function () {
            stopSound();
            closeModal(winModal);
        }, 8000);
    }
}

function stopSound() {
    let audioElement = document.querySelector('audio');
    if (audioElement) {
        audioElement.pause();
        audioElement.currentTime = 0;
    }
}

function closeModal(winModal) {
    winModal.style.display = "none";
}

function nameAlert() {
    if(!giveName) {
        return '';
    }
    let name = prompt('Bitte schreibe deinen Namen ein.', person);
    if (name !== null && name !== '') {
        return ' ' + name;
    }
    return '';
}

function drawTriangle() {
    fill('#000000');
    triangle((width / 2) - 25, 55, (width / 2) + 25, 55, width / 2, 105);
    pixelColor = get(450, 440);
}

function winningMessage(arr) {
    document.getElementById('winning-message').innerHTML = "Gratulation"+person+", du hast " + arr[0] + " gewonnen!";
    document.getElementById('winning-image').src = "/uploads/images/fortune_wheel_prices/" + arr[1];
    document.getElementById('winning-image').alt = arr[0];
}
