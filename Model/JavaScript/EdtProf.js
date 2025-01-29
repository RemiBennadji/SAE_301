function setCookie(date,jour,heure){
    //console.log(date,jour,heure);

    date = new Date(date);
    if (jour === "Mardi") { date.setDate(date.getDate() + 1); }
    else if (jour === "Mercredi") { date.setDate(date.getDate() + 2); }
    else if (jour === "Jeudi") { date.setDate(date.getDate() + 3); }
    else if (jour === "Vendredi") { date.setDate(date.getDate() + 4); }

    const day = date.getDate().toString().padStart(2, "0");
    const month = (date.getMonth() + 1).toString().padStart(2, "0");
    const year = date.getFullYear();
    date = `${year}-${month}-${day}`; //STR sous forme : YYYY-MM-DD

    //console.log(date);
    sessionStorage.setItem('reportDate', date);
    sessionStorage.setItem('reportHeure', heure);

    document.location.href = "../../View/HTML/demandePage.php";
}