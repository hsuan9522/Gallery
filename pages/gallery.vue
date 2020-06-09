<template>
	<transition name="home-link">
		<div class="gallery-wrapper background">
			<transition name="opacity" appear>
				<div class="back" v-show="Boolean_goback">
					<font-awesome-icon icon="reply" @click="$router.go(-1)"></font-awesome-icon>
				</div> 
			</transition>
			<transition name="gallery" appear>
				<div id="category-wrapper" class="category-wrapper">
					<div class="block-container" v-for="(category_item,index) in category_list" :key="index" :class="addMoveClass(index)">
						<div @click="categoryClick($event,category_item.name)" class="block-inner">
							<figure :class="category_item.name">
								<img :src="category_item.url" alt="">
							</figure>
							<transition name="gallery-detail" appear>
								<div class="inner-info" v-show="Boolean_category_detail">
									<div class="_title font_Raleway">- {{category_item.name}} -</div>
									<div class="_detail font_RoundFont">{{category_item.detail}}</div>
								</div>
							</transition>
						</div>
					</div>
				</div>
			</transition>
			<transition name="more-picture" appear>
				<div class="more-picture-wrapper" v-show="Boolean_more_gallery">
					<div @click="removeAddedClass" class="close-more-picture">&times;</div>
					
					<VueGallery :images="category_list_url" :index="index" @close="index = null"></VueGallery>
					<div id="scroll" class="_container" @scroll="renderMore($event)">
						<transition-group name="opacity">
							<div class="_container-img" v-for="(picture,imageIndex) in category_list_item" :key="imageIndex" @click="index = imageIndex">
								<div class="country-name font_Marker" v-if="picture.country_first">{{picture.country}}</div>
								<img :src="picture.small_url"/>
							</div>
						</transition-group>
						<picLoading :loading="Boolean_loading"/>
					</div>
				</div>
			</transition>
		</div>
	</transition>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';
import picLoading from '../components/picture-loading'

export default {
	transition: 'home-link',
	components: {
		picLoading,
	},
	data() {
		return {
			index: null,
			Boolean_category_detail: true,
			Boolean_more_gallery: false,
			Boolean_loading: false,
			Boolean_goback: true,
			now_folder_name: '',
			now_page: 1,
			total_page: 0,
			photography_country_name: '',
			category_list: [
				{
					name: 'Photography',
					url: '/images/carousel/Orlando/IMG_0105.jpg',
					detail: '業餘隨手拍，把眼裡看到的每一瞬間記錄在此。',
				},
				{
					name: 'Sketch',
					url: '/images/gallery/sketch/2017_sketch_usa_winooski.JPG',
					detail: '在美國打工旅遊時空閒的隨意寫生，不打稿、沒有橡皮擦，一切隨興所至。',
				},
				{
					name: 'Inktober',
					url: '/images/gallery/inktober/IMG_3124.jpg',
					detail: '在寮國金三角時，在極其無聊的生活裡給自己的每日挑戰，時長1-2個月不等。',
				},
				{
					name: 'WaterColor',
					url: '/images/gallery/watercolor/2016_watercolor_anime_totoro.jpg',
					detail: '色彩總是那麼不容易的掌控，因此畫的次數並不多，不過仍持續努力進步中。',
				},
				{
					name: 'ColorPencil',
					url: '/images/gallery/colorpencil/2014_colorpencil_anime_howl.jpg',
					detail: '相較於水彩，它相對簡單一些，不過近期也很少畫到了。',
				},
				{
					name: 'Pen',
					url: '/images/gallery/pen/2017_pen_movie_finesthours.JPG',
					detail: '決大部分的作品呈現方式，簡便的工具就可以畫出，也是我最鍾愛的方式。',
				},
			],
			router_name: [],
			category_list_item: [],
			category_list_url: [],
		}

	},
	beforeRouteLeave(to, from, next) {
		this.router_name.to = to.name;
		this.router_name.from = from.name;
		this.$store.dispatch('routerName', this.router_name);
		next();
	},
	mounted() {

		Array.from(document.getElementsByClassName("block-inner")).forEach(function (inner) {
			let direction = [1, -1];
			let x = 0, y = 0;
			let img_x = 0, img_y = 0;
			let style = '';

			inner.onmousemove = function (event) {
				let img_max_move = 20;
				y = (event.offsetX - (inner.offsetWidth / 2)) * (0.3 / (inner.offsetWidth / 2));
				x = (event.offsetY - (inner.offsetHeight / 2)) * (0.3 / (inner.offsetHeight / 2));
				style = "rotateX(" + -1 * x + "deg) rotateY(" + y + "deg)";
				if (!inner.classList.contains('figure-height')) {
					inner.style.transform = style;
					img_max_move = 45;
				}

				img_x = (event.offsetX - (inner.offsetWidth / 2)) * (img_max_move / (inner.offsetWidth / 2));
				img_y = (event.offsetY - (inner.offsetHeight / 2)) * (img_max_move / (inner.offsetHeight / 2));
				inner.firstChild.firstChild.style.margin = "0 " + img_x + "px " + img_y + "px 0";
			};

			inner.onmouseleave = function () {
				inner.style = "";
				inner.firstChild.firstChild.style = "";
			};

		});

		document.getElementsByClassName('close')[0].innerHTML = '&times;';

	},
	methods: {
		...mapActions([
			'routerName'
		]),

		categoryClick(event, name) {
			if (this.now_folder_name == name) {
				return;
			}
			this.removeAddedClass();
			this.index = null;
			this.category_list_url = [];
			this.category_list_item = [];
			this.now_page = 1;
			this.photography_country_name = '';
			this.Boolean_goback = false;
			// 
			this.now_folder_name = name;
			this.Boolean_more_gallery = true;
			this.Boolean_category_detail = false;
			document.getElementById('category-wrapper').classList.add('active');
			this.moveCategory(event);
			this.getPicture(name, this.now_page);

		},
		moveCategory(event) {
			// console.log(event.target);
			event.target.parentNode.classList.add('active');
			event.target.firstChild.classList.add('active');

			let i = 0;
			document.querySelectorAll(".block-container").forEach(function (el) {
				// console.log("add resize");
				el.classList.add('resize');
				// console.log('resize', i, el.classList);
				if (!el.classList.contains('active')) {
					el.classList.add('click-move-' + i);
					i++;
				}
				// console.log('click-move', i, el.classList);
			});
			// console.log('end', document.querySelectorAll(".block-container")[0].classList);

			document.querySelectorAll(".block-inner").forEach(function (el) {
				// console.log("add figure-height");

				el.classList.add('figure-height');
			});

		},
		addMoveClass(index) {
			return 'move-' + index;
		},
		removeAddedClass() {
			let element;
			this.Boolean_category_detail = true;
			this.Boolean_more_gallery = false;
			this.Boolean_goback = true;

			this.now_folder_name = '';
			document.getElementById('category-wrapper').classList.remove('active');
			document.getElementById('category-wrapper').style = "";

			for (let i = 0; i < 6; i++) {
				element = document.getElementsByClassName("click-move-" + i)[0];
				if (element) {
					document.getElementsByClassName("click-move-" + i)[0].classList.remove("click-move-" + i);
				}
			}
			document.querySelectorAll(".block-container").forEach(function (el) {
				el.classList.remove('active');
				el.classList.remove('resize');
			});
			document.querySelectorAll(".block-inner").forEach(function (el) {
				el.classList.remove('figure-height');
				el.firstChild.classList.remove('active');
			});
		},
		getPicture(foldername, page) {
			let _this = this;
			_this.Boolean_loading = true;
			axios({
				url: `/php/api/getGalleryPic.php`,
				method: 'POST',
				data: {
					foldername: foldername,
					page: page,
					country_name: _this.photography_country_name
				}
			}).then(function (response) {
				if (foldername != 'Photography') {
					_this.total_page = response.data.arg['total_page'];
					for (let index in response.data.data) {
						_this.category_list_item.push(response.data.data[index]);
						_this.category_list_url.push(response.data.data[index]['small_url']);
					}
				} else {
					_this.total_page = response.data.photoset.pages;
					for (let index in response.data.photoset.photo) {
						_this.category_list_item.push(response.data.photoset.photo[index]);
						_this.category_list_url.push(response.data.photoset.photo[index]['large_url']);
					}
					_this.photography_country_name = response.data.photoset.photo[response.data.photoset.photo.length - 1]['country'];
					// console.log(_this.country_name)
				}
				_this.Boolean_loading = false;
			})
		},
		renderMore(event) {
			let target = event.target;
			if (target.scrollHeight - target.scrollTop == target.offsetHeight && this.now_page < this.total_page) {
				console.log('///')
				this.Boolean_loading = true;

				this.getPicture(this.now_folder_name, ++this.now_page);
			}
		},

	}
};
</script>


