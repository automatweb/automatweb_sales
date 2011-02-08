$(document).ready(function() {
	(function() {
		function calc_rates(){
			for(i in declaration.configuration.rates){
				rate = declaration.configuration.rates[i];
				switch(rate.category){
					//	CONTACT_LEARNING
					case 1:
					case "1":
						sum = 0;
						for(applicable in rate.applicables){
							if($("input[name='professions["+applicable+"][active]']:checked").size() != 0){
								sum = sum + $("input[name='professions["+applicable+"][load]']").parseNumber().pop() * rate.applicables[applicable];
							} else if($("input[name='competences["+applicable+"][active]']:checked").size() != 0){
								sum = sum + rate.applicables[applicable]*1;
							}
						}
						declaration.points[rate.id] = sum * declaration.contact_learning.total;
						break;

					//	E_LEARNING
					case 2:
					case "2":
						sum = 0;
						for(applicable in rate.applicables){
							if($("input[name='professions["+applicable+"][active]']:checked").size() != 0){
								sum = sum + $("input[name='professions["+applicable+"][load]']").parseNumber().pop() * rate.applicables[applicable];
							} else if($("input[name='competences["+applicable+"][active]']:checked").size() != 0){
								sum = sum + rate.applicables[applicable]*1;
							}
						}
						declaration.points[rate.id] = sum * declaration.e_learning.total;
						break;

					//	THESIS_SUPERVISED
					case 3:
					case "3":
						sum = 0;
						for(applicable in rate.applicables){
							if($("input[name='professions["+applicable+"][active]']:checked").size() != 0){
								sum = sum + $("input[name='professions["+applicable+"][load]']").parseNumber().pop() * rate.applicables[applicable];
							} else if($("input[name='competences["+applicable+"][active]']:checked").size() != 0){
								sum = sum + rate.applicables[applicable]*1;
							}
						}
						thesis_count = 0;
						for(i in rate.thesis_categories){
							if(typeof declaration.thesises.defended[rate.thesis_categories[i]] != "undefined")
							{
								for(year in declaration.thesises.defended[rate.thesis_categories[i]]){
									if(rate.years.length == 0 || typeof rate.years[year] != "undefined"){
										thesis_count += declaration.thesises.defended[rate.thesis_categories[i]][year]*1;
									}
								}
							}
						}
						declaration.points[rate.id] = sum * thesis_count;
						break;
						
					//	THESIS_OPPOSING
					case 4:
					case "4":
						sum = 0;
						for(applicable in rate.applicables){
							if($("input[name='professions["+applicable+"][active]']:checked").size() != 0){
								sum = sum + $("input[name='professions["+applicable+"][load]']").parseNumber().pop() * rate.applicables[applicable];
							} else if($("input[name='competences["+applicable+"][active]']:checked").size() != 0){
								sum = sum + rate.applicables[applicable]*1;
							}
						}
						thesis_count = 0;
						for(i in rate.thesis_categories){
							if(typeof declaration.thesises.opposed[rate.thesis_categories[i]] != "undefined")
							{
								for(year in declaration.thesises.opposed[rate.thesis_categories[i]]){
									if(rate.years.length == 0 || typeof rate.years[year] != "undefined"){
										thesis_count += declaration.thesises.opposed[rate.thesis_categories[i]][year]*1;
									}
								}
							}
						}
						declaration.points[rate.id] = sum * thesis_count;
						break;

					//	PUBLICATIONS
					case 5:
					case "5":
						sum = 0;
						for(i in rate.publication_categories){
							category = rate.publication_categories[i];
							for(year in declaration.publications[category]){
								if(rate.years.length == 0 || typeof rate.years[year] != "undefined"){
									sum += declaration.publications[category][year]*1;
								}
							}
						}
						applicable_rate = 0;
						for(applicable in rate.applicables){
							if($("input[name='professions["+applicable+"][active]']:checked").size() != 0 || $("input[name='competences["+applicable+"][active]']:checked").size() != 0){
								applicable_rate = Math.max(applicable_rate, rate.applicables[applicable]);
							}
						}
						declaration.points[rate.id] = sum * applicable_rate;
						break;
				}
			}
		}

		function calc_loads(){
			calc_rates();
			// arr(declaration.points);
			total = [0, 0, 0];

			for(rate in declaration.points){
				total[declaration.configuration.rates[rate].type] += declaration.points[rate];
			}

			$("#wl_teaching_load").val(total[1]);
			$("#wl_research_load").val(total[2]);
			$("#wl_total_load").val(total[1] + total[2]);

			required = 0;
			$.each(declaration.professions, function(id, profession){
				if($("input[name='professions["+id+"][active]']:checked").size() != 0){
					required += profession.load*1;
				}
			});
			$("#wl_difference").val(required - $("#wl_total_load").val());
		}

		$("input[type=text][name^=professions]").keyup(function(){
			calc_loads();
		});
		$(":checkbox").click(function(){
			calc_loads();
		});

		calc_loads();
	})();
});