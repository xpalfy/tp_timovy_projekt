const axios = require('axios');
const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

// Setup folders
const tempDir = './avatars_temp';
const finalDir = './avatars_100x100';
if (!fs.existsSync(tempDir)) fs.mkdirSync(tempDir);
if (!fs.existsSync(finalDir)) fs.mkdirSync(finalDir);

// Avatar options
const options = {
  topType: ['NoHair', 'ShortHairShortCurly', 'LongHairStraight', 'Hat'],
  accessoriesType: ['Blank', 'Kurt', 'Round', 'Sunglasses'],
  hairColor: ['BrownDark', 'Blonde', 'Black', 'Red'],
  facialHairType: ['Blank', 'BeardLight', 'MoustacheFancy'],
  clotheType: ['Hoodie', 'GraphicShirt', 'Overall', 'BlazerShirt'],
  eyeType: ['Happy', 'Squint', 'Wink', 'Surprised'],
  eyebrowType: ['Default', 'RaisedExcited', 'SadConcerned', 'UnibrowNatural'],
  mouthType: ['Smile', 'Serious', 'Twinkle', 'Disbelief'],
  skinColor: ['Light', 'Brown', 'DarkBrown', 'Pale']
};

// Generate a random parameter set
const getRandomParams = () =>
  Object.entries(options)
    .map(([key, values]) => `${key}=${values[Math.floor(Math.random() * values.length)]}`)
    .join('&');

const usedParams = new Set();

const generateAndResizeAvatars = async (target = 1000) => {
  let count = 0;
  while (count < target) {
    const params = getRandomParams();
    if (usedParams.has(params)) continue;
    usedParams.add(params);

    const url = `https://avataaars.io/?avatarStyle=Circle&${params}`;
    const tempPath = path.join(tempDir, `avatar_${count + 1}.png`);
    const finalPath = path.join(finalDir, `avatar_${count + 1}.png`);

    try {
      const response = await axios.get(url, { responseType: 'stream' });
      const tempWrite = fs.createWriteStream(tempPath);
      await new Promise((resolve, reject) => {
        response.data.pipe(tempWrite);
        tempWrite.on('finish', resolve);
        tempWrite.on('error', reject);
      });

      // Resize to 100x100
      await sharp(tempPath)
        .resize(100, 100)
        .toFile(finalPath);

      console.log(`Saved & resized avatar ${count + 1}`);
      count++;
    } catch (error) {
      console.error(`Error processing avatar ${count + 1}:`, error.message);
    }
  }

  // Optional: clean up temporary files
  fs.rmSync(tempDir, { recursive: true, force: true });
};

generateAndResizeAvatars();
