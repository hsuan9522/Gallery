<template>
    <span>
        <svg id="animate_map" width="100vw" height="80vh" viewBox="200 -100 3000 1400" xmlns="http://www.w3.org/2000/svg" v-show="showElement($router.name)">
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
    </span>
</template>

<script>
export default {
    data() {
        return {
            now_url_name: '',
            link_button: [
                {
                    name: 'home',
                    url: '/',
                    show: true,
                    icon_image: 'images/btn_image.png'
                },
                {
                    name: 'gallery',
                    url: '/gallery',
                    show: true,
                    icon_image: 'images/btn_image.png'
                },
                {

                    name: 'article',
                    url: '',
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
        }
    },
    mounted() {
        this.now_url_name = this.$router.name;
    },
    methods: {
        backgroundAnime(event, name) {
            if (name != 'gallery') {
                return;
            }
            event.target.classList.add('active');
            document.getElementById('link-background').classList.add("enter-active");
        },
        showLinkElement(index) {
            if (this.now_url_name != 'index') {
                return;
            }
            for (let i = 0; i < 5; i++) {
                if (i == index) {
                    this.link_button[i].show = true;
                } else {
                    this.link_button[i].show = false;
                }
            }
        },
        hideLinkElement() {
            if (this.now_url_name != 'index') {
                return;
            }
            for (let i = 0; i < 5; i++) {
                this.link_button[i].show = false;
            }
        },
        onClickButton(name, url) {
            if (name == 'instagram') {
                location.href = url;
            } else {
                this.$router.push(url);
            }
        },

    }

}
</script>
