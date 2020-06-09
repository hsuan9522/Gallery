<template>
	<section class="index-wrapper">
		<svg id="animate_map" width="100vw" height="80vh" viewBox="200 -100 3000 1400" xmlns="http://www.w3.org/2000/svg" v-show="link_button[4].show">
			<defs>
				<filter id="f3" x="0" y="0" width="200%" height="100%">
					<feOffset result="offOut" in="SourceAlpha" dx="5" dy="7" />
					<feGaussianBlur result="blurOut" in="offOut" stdDeviation="5" />
					<feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
				</filter>
			</defs>
			<!-- <path d="M1250,700a450,80 -22.5 1,0 900,0a450,80 -22.5 1,0 -900,0" fill="transparent" stroke="black"/> -->
			<g>
				<image id="satellit" xlink:href="/images/satellit.png" x="0" y="0" height="150px" width="150px" style="filter:url(#f3)"/>

				<animateMotion
					path="M1350,600a350,70 -22.5 1,0 700,0a350,70 -22.5 1,0 -700,0"
					dur="6s"
					begin="0s"
					rotate=""
					repeatCount="indefinite" 
					fill="freeze"/>
				
				<animateTransform 
					attributeName="transform" 
					begin="0s"
					type="scale"
					values="0;1.5;1.5;0;0"
					dur="6s"
					additive="sum"
					repeatCount="indefinite" 
	           	/>
				<animateTransform 
					attributeName="transform" 
					begin="0s"
					type="rotate"
					values="0;-20;5;25;0"
					dur="6s"
					additive="sum"
					repeatCount="indefinite" 
	           	/>
			</g>
			<!-- <polygon fill="#FFD41D" points="97.3,0 127.4,60.9 194.6,70.7 145.9,118.1 157.4,185.1 97.3,153.5 37.2,185.1 48.6,118.1 0,70.7 67.2,60.9">
				<animate repeatDur="01:30"  begin='2s' attributeName="points" dur="1s" to="110,58.2 147.3,0 192.1,29 141.7,105.1 118.7,139.8 88.8,185.1 46.1,156.5 0,125 23.5,86.6 71.1,116.7"/>
			</polygon> -->
		</svg>
		<transition name="word" appear>
			<div class="link-element word">
				<img src="/images/welecome.png" alt="">
			</div>
		</transition>
		<transition name="item" appear>
			<div>
				<div class="link-element">
					<div class="copyright font_Raleway">illustration by husan</div>
				</div>
				<div id="aviator" class="link-element aviator">
					<img id="aviator_img" src="/images/home_item.png" alt="">
				</div>
				<div id="home_bg" class="link-element bg animate">
					<img src="/images/home_bg.png" alt="">
				</div>
				<div id="home_bg2" class="link-element bg">
					<img src="/images/home_bg2.png" alt="">
				</div>
			</div>
		</transition>
		<!--  -->
		<div id="link-background" class="link-background hover-active hover-active2">
			<div class="btn-wrapper">
				<svg v-for="(link_button, index) in link_button" :key="index" class="test" width="62" height="62" viewBox="0 25 102 50" xmlns="http://www.w3.org/2000/svg">
					<defs>
						<pattern :id="link_button.name" x="0%" y="0%" height="100%" width="100%" viewBox="0 0 100 100">
							<image x="0%" y="0%" width="100" height="100" :xlink:href="link_button.icon_image"></image>
						</pattern>
					</defs>
						
					<circle 
						class="link-button" 
						cx="50" cy="50" r="47" 
						:fill="'url(#'+link_button.name +')'"
						@mouseenter="showLinkElement(index)"
						@mouseout="hideLinkElement"
						@click="backgroundAnime($event,link_button.name);onClickButton(link_button.name,link_button.url)"
					/>
				</svg>
			</div>
		</div>
		<transition name="modal" appear>
			<div class="about-wrapper" v-show="Boolean_aboutme">
				<transition name="opacity" appear>
					<div class="about font_RoundFont">
						<span>
							<div class="_title">About Here</div>
							<div class="_detail">
								Hi, welcome to here! Here is the version 2 of <router-link to="hsuan.at.tw">hsuan.at.tw</router-link> which is my first website when I'm just strating learning front-end. 
								This time I tried to build the website via Vue.js. 
								And the functions don't have much difference from the previous one, because I just use another language to build it again. 
								I like to travel around so you can see many pictures which most are shooting by me, and some by my friends in the gallery.
								And I also like to drawing, so you also can see many drawings in here too.
								The article page doesn't open now, when it be done I'll update it again.
							</div>
						</span>
						<span>
							<div class="_title">About My Skill</div>
							<div class="_detail">
								<div v-for="(bar,index) in skill_bar" :key="index">
									<label>{{bar.skill}}</label>
									<b-progress :value="bar.value" :variant="bar.variant" striped></b-progress>
								</div>
							</div>
						</span>
						<div class="_close">
							<font-awesome-icon class="__icon" icon="times" @click="Boolean_aboutme=false"></font-awesome-icon>
						</div>
					</div>
				</transition>
			</div>
		</transition>
	</section>
</template>
<script>
import AppLogo from "~/components/AppLogo.vue";
import { mapActions } from 'vuex';

export default {
	data() {
		return {
			Boolean_aboutme: false,
			link_button: [
				{
					name: 'gallery',
					url: '/gallery',
					show: true,
					icon_image: 'images/btn_image.png'
				},
				{

					name: 'article',
					url: '/article',
					show: false,
					icon_image: 'images/btn_article.png'
				},
				{
					name: 'about',
					url: '',
					show: false,
					icon_image: 'images/btn_aboutme.png'
				},
				{
					name: 'instagram',
					url: 'https://www.instagram.com/imnt_a_superhero/',
					show: false,
					icon_image: 'images/btn_link.png'
				},
				{
					name: 'map',
					url: '/map',
					show: false,
					icon_image: 'images/btn_map.png'
				},
			],
			skill_bar: [
				{
					skill: 'CSS / SCSS',
					value: 90,
					variant: 'info'
				},
				{
					skill: 'JAVASCRIPT / VUE',
					value: 75,
					variant: 'danger'
				},
				{
					skill: 'SQL',
					value: 40,
					variant: 'warning'
				},
				{
					skill: 'PHP',
					value: 40,
					variant: 'success'
				},
				{
					skill: 'DOCKER',
					value: 30,
					variant: 'primary'
				},
			],
			router_name: {},

		}
	},
	beforeRouteLeave(to, from, next) {
		this.router_name.to = to.name;
		this.router_name.from = from.name;
		this.$store.dispatch('routerName', this.router_name);
		next();
	},
	mounted() {
		if (screen.width <= 699) {
			alert('This new website doesn\'t provide for mobile device yet.\nPlease use laptop or computer!\n\nRedirct to old address!')
			window.location.href = "http://hsuan.at.tw";
		}
		let movementStrength = 25;
		let height = movementStrength / window.innerHeight;
		let width = movementStrength / window.innerWidth;
		document.getElementById('aviator').onmousemove = function (e) {
			let pageX = e.pageX - (window.innerHeight / 2);
			let pageY = e.pageY - (window.innerWidth / 2);
			let newvalueX = width * pageX * -1 - 15;
			let newvalueY = height * pageY * -1 - 10;
			var ax = -(window.innerWidth / 2 - e.pageX) / 30;
			var ay = (window.innerHeight / 2 - e.pageY) / 25;
			document.getElementById('home_bg').style.left = newvalueX + 60 + "px ";
			document.getElementById('home_bg').style.top = newvalueY - 30 + "px";
			document.getElementById('aviator').style.transform = "rotateY(" + ax + "deg) rotateX(" + ay + "deg)";
			document.getElementById('aviator_img').style.filter = "drop-shadow(" + ax / Math.abs(ax) * -6 + "px " + ay / Math.abs(ay) * 5 + "px 3px grey)";
			// document.getElementById('home_bg2').style.left = newvalueX + 60 + "px ";
			// document.getElementById('home_bg2').style.top = newvalueY - 30 + "px";
		};
		document.getElementById('aviator').onmouseleave = function (e) {
			document.getElementById('aviator').style.transform = "";
			document.getElementById('aviator_img').style.filter = "";

			// document.getElementById('home_bg').style.top = '';
			// document.getElementById('home_bg').style.transform = "";

		};

	},
	methods: {
		...mapActions([
			'routerName'
		]),
		backgroundAnime(event, name) {
			if (name != 'gallery' && name != 'article') {
				return;
			}
			event.target.classList.add('active');
			document.getElementById('link-background').classList.add("enter-active");
		},
		removeWrapperClass() {
			document.getElementById('link-background').classList.remove("hover-active");
		},
		showLinkElement(index) {
			for (let i = 0; i < 5; i++) {
				if (i == index) {
					this.link_button[i].show = true;
				} else {
					this.link_button[i].show = false;
				}
			}
		},
		hideLinkElement() {
			for (let i = 0; i < 5; i++) {
				this.link_button[i].show = false;
			}
		},
		onClickButton(name, url) {
			if (name == 'instagram') {
				location.href = url;
			} else if (name == 'about') {
				this.Boolean_aboutme = true;
			} else {
				this.$router.push(url);
			}
		}
	}

};
</script>


