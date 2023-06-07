@push('script')
<script>
    (function () {
        let labelDanger, headingColor, labelColor, lagendColor, borderColor, shadeColor, grayColor;
        if (isDarkStyle) {
            cardColor = config.colors_dark.cardColor;
            labelColor = config.colors_dark.textMuted;
            legendColor = config.colors_dark.bodyColor;
            headingColor = config.colors_dark.headingColor;
            borderColor = config.colors_dark.borderColor;
            // primaryColor = config.colors_dark.primaryColor;
            // labelPrimaryColor = config.colors_dark.labelPrimaryColor;
        } else {
            cardColor = config.colors.cardColor;
            labelColor = config.colors.textMuted;
            legendColor = config.colors.bodyColor;
            headingColor = config.colors.headingColor;
            borderColor = config.colors.borderColor;
            // primaryColor = config.colors.primaryColor;
            // labelPrimaryColor = config.colors.labelPrimaryColor;
        }

        const studentGenderChartEl = document.querySelector('#studentGender'),
        studentGenderChartConfig = {
        chart: {
            height: 170,
            width: 180,
            parentHeightOffset: 0,
            type: 'donut'
        },
        labels: ['Laki-laki', 'Perempuan'],
        series: @json($dataGender),
        colors: [
            config.colors_label.info,
            config.colors.info,
            // chartColors.donut.series3,
            // chartColors.donut.series4
        ],
        stroke: {
            width: 0
        },
        dataLabels: {
            enabled: false,
            formatter: function (val, opt) {
            return parseInt(val) + '%';
            }
        },
        legend: {
            show: false
        },
        tooltip: {
            theme: false
        },
        grid: {
            padding: {
            top: 15,
            right: -20,
            left: -20
            }
        },
        states: {
            hover: {
            filter: {
                type: 'none'
            }
            }
        },
        plotOptions: {
            pie: {
            donut: {
                size: '70%',
                labels: {
                show: true,
                value: {
                    fontSize: '1.375rem',
                    fontFamily: 'Public Sans',
                    color: headingColor,
                    fontWeight: 600,
                    offsetY: -15,
                    formatter: function (val) {
                    return parseInt(val) + '%';
                    }
                },
                name: {
                    offsetY: 20,
                    fontFamily: 'Public Sans'
                },
                total: {
                    show: true,
                    showAlways: true,
                    color: headingColor,
                    fontSize: '.8125rem',
                    label: 'Total',
                    fontFamily: 'Public Sans',
                    formatter: function (w) {
                    return @json($student->count());
                    }
                }
                }
            }
            }
        },
        responsive: [
            {
            breakpoint: 1025,
            options: {
                chart: {
                height: 172,
                width: 160
                }
            }
            },
            {
            breakpoint: 769,
            options: {
                chart: {
                height: 178
                }
            }
            },
            {
            breakpoint: 426,
            options: {
                chart: {
                height: 147
                }
            }
            }
        ]
        };
    if (typeof studentGenderChartEl !== undefined && studentGenderChartEl !== null) {
        const studentGenderChart = new ApexCharts(studentGenderChartEl, studentGenderChartConfig);
        studentGenderChart.render();
    }
    })();
</script>
@endpush

<div class="card h-100">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
            <h5 class="mb-0">Santri</h5>
            <small class="text-muted">Berdasarkan Gender</small>
        </div>
    </div>
    <div class="card-body d-flex justify-content-center align-items-center">
        <div id="studentGender"></div>
    </div>
</div>