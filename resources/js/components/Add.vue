<template>
    <div class="add-form">
        <h6>{{ coachName == 'no' ? 'Записаться без тренера' : 'Добавить тренировку'}}</h6>
        <br>
        <form @submit.prevent="store">
            <div class="form-group">
                <label for="date">выберите дату</label>
                <datepicker :monday-first="true" id="date" v-model="form.date"></datepicker>
            </div>
            <div class="form-group">
                <label for="startTime">время начала</label><br>
                <vue-timepicker format="HH:mm" input-width="220px" id="startTime" v-model="form.startTime" hide-clear-button close-on-complete></vue-timepicker>
            </div>
            <!-- <div class="form-group" v-if="coachName != 'no'">
                <label for="stopTime">время конца</label><br>
                <vue-timepicker format="HH:mm" input-width="220px" id="stopTime" v-model="form.stopTime" hide-clear-button close-on-complete></vue-timepicker>
            </div> -->
            <div class="form-group" v-if="coachName != 'no'">
                <label for="title">Название</label><br>
                <input required type="text" class="form-control" id="title" v-model="form.title">
            </div>
            <div class="form-group" v-if="clientName != 'no'">
                <label for="title">Имя</label><br>
                <input required type="text" class="form-control" id="title" v-model="form.client" readonly>
            </div>
            <div class="form-group" v-if="coachName != 'no'">
                <label for="coach">имя тренера</label><br>
                <input required type="text" class="form-control" id="coach" v-model="form.coach" readonly>
            </div>
            <button type="submit" class="btn btn-success">{{ coachName == 'no' ? 'Записаться' : 'Добавить'}}</button>
        </form>
    </div>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import VueTimepicker from 'vue2-timepicker/src/vue-timepicker.vue';
import moment from "moment";

export default {
    props: ['coachName', 'clientName', 'userId'],
    data() {
        return {
            form: {
                date: null,
                startTime: '',
                stopTime: '',
                title: '',
                client: this.clientName,
                coach: this.coachName,
                creator: this.userId
            }
        }
    },
    components: {
        Datepicker,
        VueTimepicker
    },
    methods: {
        store() {
            let request = this.form;

            if (this.coachName == 'no') {
                request.title = 'Тренировка';
            }

            let stopHour = parseInt(request.startTime.substring(0,2), 10) + 1;
            request.stopTime = stopHour + request.startTime.substring(2, request.startTime.length);

            request.date = moment(request.date).format('YYYY-MM-DD');
            axios.post('/api/trainings', request, {
                headers: {
                    "Content-type": "application/json"
                }
            }).then(res => {
                alert(res.data.msg);
                if (res.data.status) {
                    this.$emit('reload');
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
