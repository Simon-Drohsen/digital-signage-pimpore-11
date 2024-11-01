import Carousel from 'stimulus-carousel';
import { Application } from "@hotwired/stimulus"
import { definitionsFromContext } from "@hotwired/stimulus-webpack-helpers"

window.Stimulus = Application.start()
const context = require.context("./js/controllers", true, /\.js$/)
Stimulus.load(definitionsFromContext(context))
Stimulus.register('carousel', Carousel)
