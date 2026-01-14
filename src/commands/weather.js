import { SlashCommandBuilder } from 'discord.js';
import axios from 'axios';

export const data = new SlashCommandBuilder()
    .setName('weather')
    .setDescription('Cek cuaca kota tertentu')
    .addStringOption(option =>
        option.setName('city')
            .setDescription('Nama kota')
            .setRequired(true));

export async function execute(interaction) {
    const city = interaction.options.getString('city');
    const apiKey = process.env.OPENWEATHER_API_KEY;

    if (!apiKey) {
        return await interaction.reply({ content: 'OpenWeatherMap API Key belum dikonfigurasi!', ephemeral: true });
    }

    try {
        const res = await axios.get(`https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric&lang=id`);
        const weather = res.data;

        const embed = {
            title: `☁️ Cuaca di ${weather.name}, ${weather.sys.country}`,
            description: `${weather.weather[0].description}`,
            fields: [
                { name: 'Suhu', value: `${weather.main.temp}°C`, inline: true },
                { name: 'Terasa Seperti', value: `${weather.main.feels_like}°C`, inline: true },
                { name: 'Kelembaban', value: `${weather.main.humidity}%`, inline: true }
            ],
            thumbnail: { url: `http://openweathermap.org/img/w/${weather.weather[0].icon}.png` },
            color: 0x3498DB
        };
        await interaction.reply({ embeds: [embed] });
    } catch (err) {
        await interaction.reply({ content: `Gagal mengambil data cuaca untuk kota: ${city}`, ephemeral: true });
    }
}
