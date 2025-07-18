# Pusher Setup Guide

## 🚀 **Step 1: Create Pusher Account**

1. Go to [https://pusher.com](https://pusher.com)
2. Sign up for a free account
3. Create a new app
4. Choose your cluster (recommend: `mt1` for US East)

## 🔧 **Step 2: Configure Environment Variables**

Add these variables to your `.env` file:

```env
# Broadcasting Configuration
BROADCAST_DRIVER=pusher

# Pusher Configuration
PUSHER_APP_ID=your-pusher-app-id
PUSHER_APP_KEY=your-pusher-app-key
PUSHER_APP_SECRET=your-pusher-app-secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_ENCRYPTED=true
```

## 📋 **Step 3: Get Your Pusher Credentials**

1. In your Pusher dashboard, go to your app
2. Click on "App Keys" in the sidebar
3. Copy the following values:
   - **App ID** → `PUSHER_APP_ID`
   - **Key** → `PUSHER_APP_KEY`
   - **Secret** → `PUSHER_APP_SECRET`
   - **Cluster** → `PUSHER_APP_CLUSTER`

## 🎯 **Step 4: Test the Setup**

1. Clear your Laravel cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. Test broadcasting:
   ```bash
   php artisan tinker
   ```
   ```php
   event(new App\Events\EquipmentCostNotificationEvent(
       App\Models\EquipmentCostNotification::first(),
       auth()->id()
   ));
   ```

## ✅ **Step 5: Verify Real-time Notifications**

1. Open your application in two browser tabs
2. Log in as different users
3. Create an equipment cost request
4. You should see real-time notifications appear instantly!

## 🆓 **Free Tier Limits**

- ✅ **100 concurrent connections** (you need ~20)
- ✅ **200,000 messages/day** (you need ~200)
- ✅ **Unlimited channels**
- ✅ **SSL encryption**
- ✅ **Global infrastructure**

## 🔧 **Troubleshooting**

### Issue: "Pusher not configured"
- Check your `.env` file has all Pusher variables
- Clear config cache: `php artisan config:clear`

### Issue: "User ID not found"
- Make sure user is logged in
- Check `meta[name="user-id"]` in page source

### Issue: Connection errors
- Verify Pusher credentials are correct
- Check cluster name matches your app
- Ensure `BROADCAST_DRIVER=pusher` is set

## 🎉 **Success!**

Once configured, you'll have:
- ⚡ **Real-time notifications** - No more polling!
- 🔔 **Instant updates** - Notifications appear immediately
- 🔊 **Sound alerts** - Audio notifications for new messages
- 📱 **Cross-device** - Works on all devices
- 🆓 **Free forever** - No monthly costs for your usage 