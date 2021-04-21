<template>
    <div class="schedule">
        <div class="filter">
            <div class="week">
                <button class="week-btn" @click="prevWeek"><i class="arrow left"></i></button>
                <p class="week-text"><span>{{filter.startDate}}</span> - <span>{{filter.stopDate}}</span></p>
                <button class="week-btn" @click="nextWeek"><i class="arrow right"></i></button>
            </div>
            <div class="dropdown">
                <button class="filter-dropdown dropdown-toggle" type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    {{ filter.coach != "all" ? filter.coach : "Все тренеры" }}
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li v-if="filter.coach != 'all'"><a class="dropdown-item" href="#" @click="filter.coach = 'all'">Все</a></li>
                    <li v-for="coach in coaches"><a v-if="filter.coach != coach" class="dropdown-item" href="#" @click="filter.coach = coach">{{ coach }}</a></li>
                </ul>
            </div>
        </div>

        <table class="schedule-table">
            <tr>
                <th class="table-clock">
                    <img height="20" width="20" src="img/clock.svg">
                </th>
                <th v-for="(e, i) in 7" class="day-header">
                    <p>{{weekDays[i]}}</p>
                    <p>{{weekDaysString[i]}}</p>
                </th>
            </tr>
            <tr v-for="time in table">
                <td class="table-time">{{ time.hour }}</td>
                <td v-for="day in weekDaysTable">
                    <div v-for="training in time[day]" class="training-card">
                        <div class="training-status">
                            {{ training.status }}
                        </div>
                        <div class="training-time">
                            {{ training.timeStart }} - {{ training.timeStop }} ({{ training.duration }})
                        </div>
                        <div class="training-title">
                            {{ training.title }}
                        </div>
                        <div class="training-coach">
                            {{ training.coach }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    data() {
        return {
            currentMonday: null,

            weekDays : [],
            weekDaysString : ['Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'],
            weekDaysTable : ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],

            filter: {
                startDate: null,
                stopDate: null,
                coach: "all",
            },

            coaches: ["Cкала", "Стетхем", "Кали Масл", "Зидан"],

            table: [
                {
                    hour: '09',
                    mon: [
                        {
                            status: "свободно",
                            timeStart: "09:00",
                            timeStop: "09:45",
                            duration: "45 мин",
                            title: "Аквааэробика",
                            coach: "Скала"
                        }
                    ],
                    tue: [],
                    wed: [],
                    thu: [
                        {
                            status: "свободно",
                            timeStart: "09:00",
                            timeStop: "10:00",
                            duration: "1 час",
                            title: "Каратэ",
                            coach: "Стетхем"
                        },
                        {
                            status: "свободно",
                            timeStart: "09:10",
                            timeStop: "09:55",
                            duration: "45 мин",
                            title: "Футбол",
                            coach: "Зидан"
                        }
                    ],
                    fri: [],
                    sat: [],
                    sun: [],
                },
                {
                    hour: '11',
                    mon: [],
                    tue: [
                        {
                            status: "свободно",
                            timeStart: "11:00",
                            timeStop: "11:55",
                            duration: "55 мин",
                            title: "Хайфи",
                            coach: "Кали Масл"
                        }
                    ],
                    wed: [],
                    thu: [],
                    fri: [],
                    sat: [],
                    sun: [],
                }
            ]
        }
    },
    methods: {
        thisWeek: function () {
            let d = new Date();
            while (d.getDay() != 1) {
                d.setDate(d.getDate() - 1);
            }

            this.currentMonday = d;

            this.filter.startDate = moment(d).format('DD.MM.YYYY');

            let d2 = new Date();
            d2.setTime(d.getTime());

            let week = [];
            week.push(d2.getDate())
            while (d2.getDay() != 0) {
                d2.setDate(d2.getDate() + 1);
                week.push(d2.getDate());
            }
            this.filter.stopDate = moment(d2).format('DD.MM.YYYY');

            this.weekDays = week;
        },

        nextWeek() {
            this.currentMonday.setDate(this.currentMonday.getDate() + 7);

            this.filter.startDate = moment(this.currentMonday).format('DD.MM.YYYY');

            // let sunday = new Date();
            // sunday.setTime(this.currentMonday.getTime());
            // sunday.setDate(sunday.getDate() + 6);
            //
            // this.filter.stopDate = moment(sunday).format('DD.MM.YYYY');

            let d = new Date();
            d.setTime(this.currentMonday.getTime());

            let week = [];
            week.push(d.getDate())
            while (d.getDay() != 0) {
                d.setDate(d.getDate() + 1);
                week.push(d.getDate());
            }
            this.filter.stopDate = moment(d).format('DD.MM.YYYY');

            this.weekDays = week;
        },

        prevWeek() {
            this.currentMonday.setDate(this.currentMonday.getDate() - 7);

            this.filter.startDate = moment(this.currentMonday).format('DD.MM.YYYY');

            // let sunday = new Date();
            // sunday.setTime(this.currentMonday.getTime());
            // sunday.setDate(sunday.getDate() + 6);
            //
            // this.filter.stopDate = moment(sunday).format('DD.MM.YYYY');

            let d = new Date();
            d.setTime(this.currentMonday.getTime());

            let week = [];
            week.push(d.getDate())
            while (d.getDay() != 0) {
                d.setDate(d.getDate() + 1);
                week.push(d.getDate());
            }
            this.filter.stopDate = moment(d).format('DD.MM.YYYY');

            this.weekDays = week;
        }
    },
    watch: {
        filter: {
            deep: true,
            handler(val) {
                console.log(val);
            }
        }
    },
    computed: {

    },
    mounted() {
        this.thisWeek();
    }
}
</script>

<style scoped>

</style>
