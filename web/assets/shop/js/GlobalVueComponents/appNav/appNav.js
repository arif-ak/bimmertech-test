var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
var appNav = new Vue({
  el: '#nav-menu',
  data: function () {
    return {
      positionTop: 0,
      links: [],
      storageLinks: [],
      currentStorageLinks: [],

      navWidth: 0,
      open: false,
    }
  },
  directives: {
    menu: {
      // directive definition
      inserted: function (el, binding, vnode) {
        if (getBrowserWidth() == 'mobile') {
          //console.log('el, binding, vnode: ', el, binding, vnode);
        }
        //el.style.cssText += 'max-height: 220px;';
      },
      update: function (el, binding, vnode) {
        if (getBrowserWidth() == 'mobile') {
          //console.log('el, binding, vnode: ', el, binding, vnode);
        }
      },
      componentUpdated: function (el, binding, vnode) {
        //console.log('el, binding, vnode: ', el, binding, vnode);
      }
    }
  },
  mounted: function () {
    var that = this;
    $('body').click(function () {
      if (that.navWidth == 645) {
        that.navWidth = 0;
        that.open = false;
      }
    });
    $(window).resize(function () {
      that.navWidth = 0;
      that.setPosutionTop();
    });
    // this.loadLinks();
    this.$nextTick(function () {
      setTimeout(() => {
        this.setPosutionTop();
        this.createNav();
        $(this.$el).find('.main-nav.mobile').css("margin-top", this.positionTop);
      }, 1000);
    });
    EventBus.$on('humburger', function () {
      if (!that.open) {
        that.openNav();
      }
    });
  },
  methods: {
    openNav: function () {
      var that = this;
      that.navWidth = 100;
      setTimeout(() => {
        if (!that.open) {
          that.navWidth = 645;
          that.open = true;
        }
      }, 400);
    },
    hideNav: function () {
      this.navWidth = 0;
      this.open = false;
    },
    setPosutionTop: function () {
      //this.positionTop = (appHeader.$el.offsetHeight !== 'undefined') ? appHeader.$el.offsetHeight : 0; // $('#appHeader')[0].clientHeight;
      this.positionTop = $('#appHeader')[0].clientHeight;
    },
    createNav: function () {
      var that = this;
      $('.bt.dropdown.item').dropdown({
        on: 'hover',
        direction: 'auto',
        selector: {
          item: '.menu-item'
        },
        onShow(i) {
          let items = $(this).find('.menu-item.product').length;
          let t = $(this).find('.show-full-view-wrapper');
          setTimeout((function () {
            if (items > 5) {
              return t.addClass('has-scroll');
            } else {
              return t.css({
                'opacity': '1'
              });
            }
          }), 500);
          that.navWidth = 645;
        }
      });
    },
    loadLinks: function () {
      var that = this;
      getRequest('menu')
        .then(data => {
          this.links = this.filterLinks(data);
          EventBus.$emit('CategoryList', this.links)
          $(document).ready(function () {
            setTimeout(() => {
              that.createNav();
            }, 500);
          });
        })

    },
    filterLinks: function (links) {
      let parsedLinks = [];
      for (let i = 0; i < links.length; i++) {
        parsedLinks.push({
          id: links[i].id,
          image: links[i].image,
          blobUrl: links[i].blobUrl,
          name: links[i].name,
          link: links[i].link,
          items: [],
          loading: false
        })
      }
      return parsedLinks;
    },
    setItem: function (data, l) {
      var that = this;
      let items = []
      var sessionLinks = this.$session.get('Menulinks');

      function pushItem(i, blobUrl) {
        items.push({
          container: data[i].container,
          description: data[i].description,
          teaser: data[i].teaser,
          id: data[i].id,
          image: data[i].image,
          link: data[i].link,
          name: data[i].name,
          price: data[i].price,
          blobUrl: blobUrl
        })
      }

      function createBase64(i) {
        fetch(window.location.origin + data[i].image)
          .then(response => response.blob())
          .then(blobData => {
            var reader = new FileReader();
            reader.readAsDataURL(blobData);
            reader.onloadend = function () {
              pushItem(i, reader.result);
              that.$session.set('Menulinks', that.links)
            }
          })
      }
      for (let i = 0; i < data.length; i++) {
        if (sessionLinks) {
          if (sessionLinks[l]) {
            let newItem = true;
            for (let k = 0; k < sessionLinks[l].items.length; k++) {
              if (sessionLinks[l].items[k].id == data[i].id) {
                newItem = false
                if (sessionLinks[l].items[k].image == data[i].image) {
                  pushItem(i, sessionLinks[l].items[k].blobUrl);
                } else {
                  createBase64(i);
                }
              }
            }
            if (newItem) {
              createBase64(i);
            }
          } else {
            createBase64(i);
          }
        } else {
          createBase64(i);
        }
      }
      return items;
    },
    loadItems: function (id, j) {
      if (this.links[j].items.length == 0 && !this.links[j].loading) {
        this.links[j].loading = 'start';
        fetch('/api2/menu/' + id)
          .then(response => response.json())
          .then(data => {
            if (data.length > 0) {
              this.links[j].items = this.setItem(data, j);
            } else {
              this.links[j].items = false;
            }
            this.links[j].loading = 'loaded';
          })
      }
    },
    loadAllItems: function () {
      for (let i = 0; i < this.links.length; i++) {
        this.loadItems(this.links[i].id, i);
      }
    }
  },
  computed: {
    filteredLinks: function () {
      return this.links;
    }
  }
})