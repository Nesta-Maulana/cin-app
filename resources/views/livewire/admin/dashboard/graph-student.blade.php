@push('script')
<script>
    (function () {
        let cardColor, headingColor, labelColor, lagendColor, borderColor, shadeColor, grayColor;
        if (isDarkStyle) {
            cardColor = config.colors_dark.cardColor;
            labelColor = config.colors_dark.textMuted;
            legendColor = config.colors_dark.bodyColor;
            headingColor = config.colors_dark.headingColor;
            borderColor = config.colors_dark.borderColor;
            shadeColor = 'dark';
            grayColor = '#5E6692';
        } else {
            cardColor = config.colors.cardColor;
            labelColor = config.colors.textMuted;
            legendColor = config.colors.bodyColor;
            headingColor = config.colors.headingColor;
            borderColor = config.colors.borderColor;
            shadeColor = '';
            grayColor = '#817D8D';
        }

        // Graph
        function barChart(arrayData, arrayNames) {
            const chartOpt = {
            chart: {
                height: 258,
                parentHeightOffset: 0,
                type: 'bar',
                stacked: true,
                toolbar: {
                show: false
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '32%',
                    borderRadius: 7,
                    startingShape: 'rounded',
                    distributed: true,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            grid: {
                show: false,
                padding: {
                top: 0,
                bottom: 0,
                left: -10,
                right: -10
                }
            },
            // colors: colorArr,
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val;
                },
                offsetY: -25,
                style: {
                    fontSize: '15px',
                    colors: [legendColor],
                    fontWeight: '600',
                    fontFamily: 'Public Sans'
                }
            },
            series: [
                {
                    name: 'Santri',
                    data: arrayData
                }
            ],
            legend: {
                show: false
            },
            xaxis: {
                categories: arrayNames,
                axisBorder: {
                    show: true,
                    color: borderColor
                },
                axisTicks: {
                    show: false
                },
                labels: {
                style: {
                    colors: labelColor,
                    fontSize: '13px',
                    fontFamily: 'Public Sans'
                }
                }
            },
            yaxis: {
                labels: {
                offsetX: -15,
                formatter: function (val) {
                    return parseInt(val / 1);
                },
                style: {
                    fontSize: '13px',
                    colors: labelColor,
                    fontFamily: 'Public Sans'
                },
                min: 0,
                max: 60000,
                tickAmount: 6
                }
            },
            responsive: [
                {
                    breakpoint: 1441,
                    options: {
                        plotOptions: {
                        bar: {
                            columnWidth: '41%'
                        }
                        }
                    }
                },
                {
                    breakpoint: 590,
                    options: {
                        plotOptions: {
                        bar: {
                            columnWidth: '61%',
                            borderRadius: 5
                        }
                        },
                        yaxis: {
                        labels: {
                            show: false
                        }
                        },
                        grid: {
                        padding: {
                            right: 0,
                            left: -20
                        }
                        },
                        dataLabels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: '400'
                        }
                        }
                    }
                }
            ]
            };
            return chartOpt;
        }

        // --------------------------------------------------------------------
        var schoolStudentsEl = document.querySelector('#schoolStudents'),
            schoolStudentsConfig = barChart(
                @json($dataSchool['count']), @json($dataSchool['name'])
            );
        if (typeof schoolStudentsEl !== undefined && schoolStudentsEl !== null) {
            var schoolStudents = new ApexCharts(schoolStudentsEl, schoolStudentsConfig);
            schoolStudents.render();
        }
       
        Livewire.on('yearUpdated', event => {
            let studentSchool = event.dataSchool.count;

            schoolStudents.updateSeries([{
                data: studentSchool
            }])
        });
        
    })();
</script>
@endpush
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
            <h5 class="mb-0">Rekap Santri</h5>
            <small class="text-muted">Berdasarkan Tahun Ajaran</small>
        </div>
        <span>
            <select wire:model='year' class="form-select form-select-sm">
                @foreach (getYearRange() as $year)
                <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </span>
    </div>
    <div class="card-body">
        <div class="p-0 ms-0 ms-sm-2">
            <div class=" " id="school_students">
                <div id="schoolStudents"></div>
            </div>
        </div>
    </div>
</div>