import 'swiper/css/bundle';
import '../css/app.css';

// Start the Stimulus application
import '../bootstrap';

// Import images using Webpack's `require.context`
const images = require.context('../images', true, /\.(jpeg|png|svg)$/);

// Optional: Load all images if needed
images.keys().forEach(images);
