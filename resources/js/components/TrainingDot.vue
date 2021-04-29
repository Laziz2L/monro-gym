<template>
  <div class="training-dot" v-bind:style="{background: 'linear-gradient( to bottom,' + this.top() + ',' + this.top() + ' 49%, white 49%, white 51%,' + this.bottom() + ' 51% )'}"></div>
</template>

<script>
export default {
    props: ['training', 'available', 'deletable'],
    data() {
        return {

        }
    },
    methods: {
        top: function() {
            if (this.training.coach_name == 'Татьяна') {
                return ' rgb(208, 52, 91) ';
            }
            else if (this.training.coach_name == 'Азалия') {
                return ' rgb(248, 102, 92) ';
            }
            else return ' rgb(140, 170, 243) ';
        },
        bottom: function() {
            if (this.available) {
                return ' rgb(65, 184, 131) ';
            }
            else if (this.deletable) {
                return 'rgb(179, 24, 24)';
            }
            else return this.top();
        },
    }
}
</script>
