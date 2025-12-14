return (int) $result['count']; Pour la fonction countAll() dans UserManager. Cette ligne va nous permettre de reutiliser cette valeur pour diviser 


Dans le fichier RefundsManager, la requete SQL de la fonction FindAll vient de claude ia : $query = $this->db->prepare('SELECT refunds.*, d.id as debtor_id,
                    d.name as debtor_name,
                    d.email as debtor_email,
                    c.id as creditor_id,
                    c.name as creditor_name,
                    c.email as creditor_email FROM refunds INNER JOIN users d ON refunds.debtor_id = d.id INNER JOIN users c ON refunds.creditor_id = c.id ');
Le prompt: Donne moi la requete sql necessaire pour avoir les infos du debiteur et du crediteur.

Pour la classe Expenses_ShareManager, le code à été réaliser en collaboration avec Gemini ou on lui indiquait ce que l'on voulait et ce qu'on ne voulait pas car le code renvoyé n'etait pas compréhensible à notre niveau

Pour le index.html.twig on s’est aidé de ChatGPT et de la doc officielle pour comprendre la logique de Bootstrap : exemple comment réaliser un bouton.
Pour le ExpenseController, aide de l’IA car lorsque j’appuyais sur le bouton check pour valider un remboursement, seule une seule valeur entrait dans la table SQL refunds, on a donc utilisé le prompt : comment faire en sorte que si j’ai deux utilisateurs qui doivent un remboursement, les deux entrent dans le tableau SQL en cas de remboursement
