 <!-- footer -->
 <?php
  include("connection/connection.php");

  $sql = "SELECT * FROM setting WHERE id = '1'";
  $foot = mysqli_query($conn, $sql);

  if ($foot && mysqli_num_rows($foot) > 0) {
    $rfoot = mysqli_fetch_assoc($foot);
  } else {
    $rfoot = [
      'country' => '',
      'city' => '',
      'address' => '',
      'email' => '',
      'whyus1' => '',
      'whyus2' => '',
      'whyus3' => '',
      'whyus4' => '',
      'facebook' => '#',
      'twitter' => '#',
      'googleplus' => '#',
      'linkedin' => '#'
    ];
  }
  ?>


 <footer>
   <div class="wrapper">
     <div class="pad1">
       <div class="pad_left1">
         <div class="wrapper">
           <article class="col_1">
             <h3>Address :</h3>
             <p class="col_address"><strong>Country:<br>
                 City:<br>
                 Address:<br>
                 Email:</strong></p>

             <p><?php echo $rfoot['country']; ?><br>
               <?php echo $rfoot['city']; ?><br>
               <?php echo $rfoot['address']; ?><br>
               <a href="mailto:<?php echo $rfoot['email']; ?>"><?php echo $rfoot['email']; ?></a>
             </p>
           </article>

           <article class="col_1 pad_left2">
             <h3>Why Us :</h3>
             <ul class="list2">
               <li><?php echo $rfoot['whyus1']; ?> </li>
               <li><?php echo $rfoot['whyus2']; ?> </li>
               <li><?php echo $rfoot['whyus3']; ?> </li>
               <li><?php echo $rfoot['whyus4']; ?> </li>
             </ul>
           </article>


           <article class="col_1 pad_left2">
             <h3>Connect with us :</h3>
             <ul class="list2">
               <li><a href="<?php echo $rfoot['facebook']; ?>" target="_blank_"> Facebook</a> </li>
               <li><a href="<?php echo $rfoot['twitter']; ?>" target="_blank_"> Twitter</a> </li>
               <li><a href="<?php echo $rfoot['googleplus']; ?>" target="_blank_"> Google Plus</a> </li>
               <li><a href="<?php echo $rfoot['linkedin']; ?>" target="_blank_"> Linked In</a> </li>

             </ul>
           </article>

         </div>

       </div>
     </div>
   </div>
 </footer>
 <!-- / footer -->
 </div>
 </div>

 <!-- Bottom Line -->
 <div class="premium-footer">
   <div class="footer-bottom">
     <p>© <?= date("Y") ?> <strong>First Alphabet</strong>. All Rights Reserved.</p>
     <p>Developed with ❤️ by <a href="https://codex404solutions.com/" target="_blank" class="dev-logo-link">
         <img src="<?= BASE_URL ?>logo/logo.png" alt="CodeX404 Solutions Logo" class="dev-logo">
       </a>
     </p>
   </div>
 </div>


 <style>
   /* Bottom Section */
   .premium-footer {
     background: linear-gradient(135deg, #0f172a, #020617);
     color: #cbd5e1;
   }

   .footer-bottom {
     border-top: 1px solid rgba(255, 255, 255, 0.1);
     /* margin-top: 50px; */
     padding-top: 15px;
     padding-bottom: 10px;
     text-align: center;
     font-size: 14px;
     color: #94a3b8;
   }

   /* =========================
        RESPONSIVE
   ========================= */

   @media (max-width: 768px) {
     .premium-footer {
       padding: 60px 0 20px;
     }

     .footer-grid {
       gap: 35px;
     }
   }

   .dev-logo {
     height: 22px;
     /* small size */
     width: auto;
     vertical-align: middle;
     transition:
       transform 0.3s ease,
       filter 0.3s ease;
   }

   /* Hover effect */
   .dev-logo-link:hover .dev-logo {
     transform: scale(1.1);
     filter: drop-shadow(0 0 5px #00bcd4);
   }
 </style>

 </body>